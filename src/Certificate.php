<?php

namespace Zerotoprod\SslCertValidator;

use Exception;
use Throwable;
use Zerotoprod\SslCertValidator\DataModels\SslCertificate;
use Zerotoprod\SslCertValidator\DataModels\Url;
use Zerotoprod\StreamContext\DataModels\Options;
use Zerotoprod\StreamContext\DataModels\Ssl;
use Zerotoprod\StreamSocket\StreamSocket;

class Certificate
{

    /**
     * Fetches the SSL certificate details from a hostname.
     *
     * This method parses the given hostname to create a URL object, sets up
     * a stream socket client to connect to the host over SSL, and retrieves
     * the SSL certificate.
     *
     * @param  string  $hostname  The hostname or URL to fetch the SSL certificate from.
     *
     * @return SslCertificate The SSL certificate information.
     */
    public static function fromHostName(string $hostname): SslCertificate
    {
        $ClientStream = StreamSocket::client(
            Url::parse($hostname)->toSsl(),
            30,
            STREAM_CLIENT_CONNECT,
            stream_context_create([
                Options::ssl => [
                    Ssl::capture_peer_cert => true
                ]
            ])
        );
        $params = $ClientStream->getParams();
        $ClientStream->close();

        $certificates = array_merge(
            [$params['options']['ssl']['peer_certificate']],
            $params['options']['ssl']['peer_certificate_chain'] ?? []
        );

        return SslCertificate::from(openssl_x509_parse($certificates[0]));
    }

    /**
     * Validate an ssl certificate.
     *
     * Example
     * ```
     * Certificate::validate('https://badssl.com/')
     * Certificate::validate('badssl.com')
     * Certificate::validate('badssl.com:999')
     * ```
     *
     *
     * @param  string  $hostname
     * @param  ?int    $time
     *
     * @return bool
     * @see https://github.com/zero-to-prod/ssl-cert-validator
     */
    public static function isExpired(string $hostname, int $time = null): bool
    {
        try {
            return self::fromHostName($hostname)->isValid($time ?: time());
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @throws Throwable
     */
    public static function hostIsValid(string $hostname): bool
    {
        try {
            StreamSocket::client(
                Url::parse($hostname)->toSsl(),
                30,
                STREAM_CLIENT_CONNECT,
                stream_context_create()
            )->close();
        } catch (Throwable $Throwable) {
            if (strpos($Throwable->getMessage(), 'did not match expected')) {
                return false;
            }
            throw $Throwable;
        }

        return true;
    }

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
     * @param  string  $hostname
     * @param  string  $cafile  Location of Certificate Authority file on local filesystem which should be used with the verify_peer context option to authenticate the identity of the remote peer.
     *
     * @return bool
     * @throws Throwable
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