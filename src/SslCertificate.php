<?php

namespace Zerotoprod\SslCertValidator;

use Exception;
use OpenSSLAsymmetricKey;
use Zerotoprod\SslCertValidator\DataModels\SocketClientRequest;
use Zerotoprod\SslCertValidator\DataModels\SslOptions;
use Zerotoprod\SslCertValidator\Helpers\StreamSocket;

class SslCertificate
{

    public const Certificate = 'Certificate';
    public const remote_socket_name = 'remote_socket_name';

    /** @var array|false $Certificate */
    public $Certificate;
    /**
     * The name of the local or remote socket
     *
     * @var false|string $remote_socket_name
     */
    public $remote_socket_name;

    public static function fromHostName(string $hostname, int $timeout = 30, bool $verifyCertificate = true): self
    {
        return StreamSocket::client(
            SocketClientRequest::from([
                SocketClientRequest::Url => parse_url(
                    !self::startsWith($hostname, ['http://', 'https://', 'ssl://'])
                        ? "https://$hostname"
                        : $hostname
                ),
                SocketClientRequest::timeout => $timeout,
                SocketClientRequest::flags => STREAM_CLIENT_CONNECT,
                SocketClientRequest::SslOptions => [
                    SslOptions::capture_peer_cert => true,
                    SslOptions::capture_peer_cert_chain => false,
                    SslOptions::SNI_enabled => true,
                    SslOptions::verify_peer => false,
                    SslOptions::verify_peer_name => $verifyCertificate,
                    SslOptions::follow_location => 1,
                ],
            ])
        )->getCertificates()[0];
    }

    /**
     * @param  string  $hostname
     * @param  int     $time
     *
     * @return bool
     */
    public static function hostIsValid(string $hostname, int $time): bool
    {
        return self::fromHostName($hostname)->isValid($time);
    }

    public function isValid(int $time): bool
    {
        return !($time < $this->validFromTime() || $time > $this->validToTime());
    }

    public function validFromTime(): int
    {
        return $this->Certificate['validFrom_time_t'];
    }

    public function validToTime(): int
    {
        return $this->Certificate['validTo_time_t'];
    }

    public function parse(): array
    {
        return openssl_x509_parse($this->Certificate);
    }

    /**
     * @return false|array
     */
    public function publicKeyDetails()
    {
        return openssl_pkey_get_details(openssl_pkey_get_public($this->Certificate));
    }

    /**
     * A wrapper for `openssl_pkey_get_public()`
     *
     * @return false|OpenSSLAsymmetricKey
     */
    public function publicKey()
    {
        return openssl_pkey_get_public($this->Certificate);
    }

    /**
     * A wrapper for `openssl_x509_fingerprint()`
     *
     * @return false|string
     */
    public function fingerprint(string $digest_algo = 'sha1', bool $binary = false)
    {
        return openssl_x509_fingerprint($this->Certificate);
    }

    public static function startsWith($haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }
}