<?php

namespace Zerotoprod\SslCertValidator;

use Throwable;
use Zerotoprod\SslCertValidator\DataModels\Certificate;
use Zerotoprod\SslCertValidator\DataModels\Url;
use Zerotoprod\StreamContext\DataModels\Options;
use Zerotoprod\StreamContext\DataModels\Ssl;
use Zerotoprod\StreamSocket\DataModels\ClientStream;
use Zerotoprod\StreamSocket\StreamSocket;

/**
 * SSL Certificate Validator.
 *
 * The `Certificate` class provides methods to fetch, validate, and verify SSL certificates for a given hostname.
 * It supports operations like checking if the certificate is expired, self-signed, or trusted by a specific Certificate Authority (CA).
 *
 * Key methods:
 * - `fromHostName`: Fetch SSL certificate details from a hostname.
 * - `isExpired`: Check if an SSL certificate is expired.
 * - `hostIsValid`: Validate if the hostname has a valid SSL certificate.
 * - `isSelfSigned`: Determine if the SSL certificate is self-signed.
 * - `isTrustedRoot`: Verify if the SSL certificate is trusted by a CA.
 *
 * Example usage:
 * ```
 * Certificate::fromHostName('https://example.com');
 * Certificate::hostIsValid('https://example.com');
 * Certificate::isExpired('example.com');
 * Certificate::isSelfSigned('example.com');
 * Certificate::isTrustedRoot('example.com', '/path/to/cafile.pem');
 * ```
 *
 * @see https://github.com/zero-to-prod/ssl-cert-validator
 */
class SslCertificate
{
    public static function from(string $hostname, array $options = []): Certificate
    {
        return self::certificates($hostname, $options)[0];
    }

    public static function fromFile($pathToCertificate): Certificate
    {
        $fileContents = file_get_contents($pathToCertificate);
        if (! strpos($fileContents, 'BEGIN CERTIFICATE')) {
            $fileContents = self::der2pem($fileContents);
        }

        return self::createFromString($fileContents);
    }
    private static function clientStream(string $address, array $options = []): ClientStream
    {
        return StreamSocket::client(
            Url::parse($address)->toSsl(),
            30,
            STREAM_CLIENT_CONNECT,
            stream_context_create([Options::ssl => $options])
        );
    }

    /**
     * Fetches SSL certificate details from a hostname.
     *
     * Parses the hostname, connects using SSL, and retrieves the certificate.
     *
     * Example:
     * ```
     * Certificate::fromHostName('https://badssl.com/')
     * Certificate::fromHostName('badssl.com')
     * Certificate::fromHostName('badssl.com:999')
     * ```
     *
     * @param  string  $hostname  The hostname or URL.
     *
     * @return array  The SSL certificate details.
     *
     * @see https://github.com/zero-to-prod/ssl-cert-validator
     */
    public static function rawCertificates(string $hostname, array $options = []): array
    {
        $ClientStream = self::clientStream($hostname, $options);
        $params = $ClientStream->getParams();
        $ClientStream->close();

        return array_merge(
            [$params['options']['ssl']['peer_certificate']],
            $params['options']['ssl']['peer_certificate_chain'] ?? []
        );
    }

    /**
     * @param  string  $hostname
     * @param  array   $options
     *
     * @return Certificate[]
     */
    public static function certificates(string $hostname, array $options = []): array
    {
        return array_map(static function ($resource) {
            return Certificate::from(openssl_x509_parse($resource));
        },
            self::rawCertificates(
                $hostname,
                array_merge([
                    Ssl::capture_peer_cert => true,
                    Ssl::verify_peer_name => false,
                    Ssl::verify_peer => false,
                ],
                    $options
                )
            ));
    }

    /**
     * Checks if the hostname's SSL certificate is valid.
     *
     * Tries to connect using SSL. Returns true if the certificate is valid, false if it doesn't match the hostname.
     * Throws an exception on other SSL errors.
     *
     * Example:
     * ```
     * Certificate::hostIsValid('https://badssl.com/')
     * Certificate::hostIsValid('badssl.com')
     * Certificate::hostIsValid('badssl.com:999')
     * ```
     *
     * @param  string  $hostname  The hostname or URL.
     *
     * @return bool  True if valid, false if the certificate doesn't match.
     *
     * @throws Throwable  On SSL connection errors (non-hostname mismatch).
     *
     * @see https://github.com/zero-to-prod/ssl-cert-validator
     */
    public static function hostIsValid(string $hostname): bool
    {
        try {
            return self::clientStream($hostname)->close();
        } catch (Throwable $Throwable) {
            if (strpos($Throwable->getMessage(), 'did not match expected')) {
                return false;
            }
            throw $Throwable;
        }
    }

    /**
     * Checks if the SSL certificate is self-signed.
     *
     * Connects to the hostname, retrieves the SSL certificate, and compares the
     * issuer with the subject. Returns true if they match (self-signed), false otherwise.
     *
     *  Example:
     *  ```
     *  Certificate::isSelfSigned('https://badssl.com/')
     *  Certificate::isSelfSigned('badssl.com')
     *  Certificate::isSelfSigned('badssl.com:999')
     *  ```
     *
     * @param  string  $hostname  The hostname or URL.
     *
     * @return bool  True if self-signed, false otherwise.
     *
     * @throws Throwable  On SSL connection or certificate parsing errors.
     *
     * @see https://github.com/zero-to-prod/ssl-cert-validator
     */
    public static function isSelfSigned(string $hostname): bool
    {
        $ClientStream = StreamSocket::client(
            Url::parse($hostname)->toSsl(),
            30,
            STREAM_CLIENT_CONNECT,
            stream_context_create([
                Options::ssl => [
                    Ssl::capture_peer_cert => true,
                    Ssl::allow_self_signed => true,
                ]
            ])
        );

        $cert = openssl_x509_parse(
            $ClientStream->getParams()['options']['ssl']['peer_certificate']
        );
        $ClientStream->close();

        return $cert['issuer'] === $cert['subject'];
    }

    /**
     * Checks if the SSL certificate is trusted by a given Certificate Authority (CA).
     *
     * Connects to the hostname and verifies the SSL certificate against the provided CA file.
     *
     *  Example:
     * ```
     * Certificate::isTrustedRoot('https://badssl.com/')
     * Certificate::isTrustedRoot('badssl.com')
     * Certificate::isTrustedRoot('badssl.com:999')
     * ```
     *
     * @param  string  $hostname  The hostname or URL.
     * @param  string  $cafile    Path to the Certificate Authority file used for verification.
     *
     * @return bool  True if the certificate is trusted by the CA, false otherwise.
     *
     * @throws Throwable  On SSL connection errors or certificate validation failures.
     *
     * @see https://github.com/zero-to-prod/ssl-cert-validator
     */
    public static function isTrustedRoot(string $hostname, string $cafile): bool
    {
        try {
            $ClientStream = StreamSocket::client(
                Url::parse($hostname)->toSsl(),
                30,
                STREAM_CLIENT_CONNECT,
                stream_context_create([
                    Options::ssl => [
                        Ssl::verify_peer => true,
                        Ssl::verify_peer_name => true,
                        Ssl::allow_self_signed => false,
                        Ssl::cafile => $cafile
                    ]
                ])
            );

            if ($ClientStream->client) {
                $ClientStream->close();

                return true;
            }
        } catch (Throwable $e) {
            $message = $e->getMessage();
            if (strpos($message, 'certificate verify failed') !== false
                || strpos($message, 'self-signed certificate in certificate chain') !== false
                || strpos($message, 'unable to get local issuer certificate') !== false
            ) {
                return false;
            }

            throw $e;
        }

        return false;
    }
}