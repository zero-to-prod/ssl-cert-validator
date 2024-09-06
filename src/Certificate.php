<?php

namespace Zerotoprod\SslCertValidator;

use Exception;
use RuntimeException;
use Throwable;
use Zerotoprod\SocketClient\DataModels\SocketClientArgs;
use Zerotoprod\SocketClient\StreamSocket;
use Zerotoprod\SslCertValidator\DataModels\SslCertificate;
use Zerotoprod\SslCertValidator\DataModels\Url;
use Zerotoprod\StreamContext\DataModels\Options;
use Zerotoprod\StreamContext\DataModels\Ssl;
use Zerotoprod\StreamContext\DataModels\StreamContextArgs;
use Zerotoprod\StreamContext\StreamContext;

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
        $Url = Url::parse($hostname);
        $SocketClient = StreamSocket::client(
            SocketClientArgs::new()
                ->set_address('ssl://'.$Url->host.':'.($Url->port ?: 443))
                ->set_timeout(30)
                ->set_flags(STREAM_CLIENT_CONNECT)
                ->set_context(
                    StreamContext::create([
                        StreamContextArgs::Options => [
                            Options::ssl => [
                                Ssl::capture_peer_cert => true
                            ]
                        ]
                    ])
                )
        );
        $params = $SocketClient->getParams();
        $SocketClient->close();

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
        $Url = Url::parse($hostname);
        try {
            $client = stream_socket_client(
                'ssl://'.$Url->host.':'.($Url->port ?: 443),
                $error_code,
                $error_message,
                30,
                STREAM_CLIENT_CONNECT,
                stream_context_create()
            );
            fclose($client);
        } catch (Throwable $e) {
            if (strpos($e->getMessage(), 'did not match expected')) {
                return false;
            }
            throw $e;
        }

        return true;
    }

    public static function isSelfSigned(string $hostname): bool
    {
        $Url = Url::parse($hostname);
        $SocketClient = StreamSocket::client(
            SocketClientArgs::new()
                ->set_address('ssl://'.$Url->host.':'.($Url->port ?: 443))
                ->set_timeout(30)
                ->set_flags(STREAM_CLIENT_CONNECT)
                ->set_context(
                    StreamContext::create([
                        StreamContextArgs::Options => [
                            Options::ssl => [
                                Ssl::capture_peer_cert => true,
                                Ssl::allow_self_signed => true,
                            ]
                        ]
                    ])
                )
        );

        $cert = openssl_x509_parse(
            $SocketClient->getParams()['options']['ssl']['peer_certificate']
        );
        $SocketClient->close();

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
        $Url = Url::parse($hostname);
        try {
            $SocketClient = StreamSocket::client(
                SocketClientArgs::new()
                    ->set_address("ssl://$Url->host:443")
                    ->set_timeout(30)
                    ->set_flags(STREAM_CLIENT_CONNECT)
                    ->set_context(
                        StreamContext::create([
                            StreamContextArgs::Options => [
                                Options::ssl => [
                                    Ssl::verify_peer => true,
                                    Ssl::verify_peer_name => true,
                                    Ssl::allow_self_signed => false,
                                    Ssl::cafile => $cafile
                                ]
                            ]
                        ])
                    )
            );

            if ($SocketClient->client) {
                $SocketClient->close();

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

    private static function extractOcspUrl(string $authorityInfoAccess): ?string
    {
        if (preg_match('/OCSP - URI:(http[^\s]+)/', $authorityInfoAccess, $matches)) {
            return $matches[1];
        }

        return null;
    }
}