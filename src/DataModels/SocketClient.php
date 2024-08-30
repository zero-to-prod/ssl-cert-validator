<?php

namespace Zerotoprod\SslCertValidator\DataModels;

use Zerotoprod\DataModel\DataModel;
use Zerotoprod\SslCertValidator\SslCertificate;

class SocketClient
{
    use DataModel;

    public const client = 'client';
    public const remote_socket_name = 'remote_socket_name';
    public const params = 'params';
    public const error_code = 'error_code';
    public const error_message = 'error_message';

    /**
     * Closed Internet or Unix domain socket connection
     *
     * @var false|resource $client
     */
    public $client;
    /**
     * The name of the local or remote socket
     *
     * @var false|string $remote_socket_name
     */
    public $remote_socket_name;
    /**
     * Retrieves parameters from a context.
     * The return value of `stream_context_get_params()`
     *
     * @var array $params
     */
    public $params;
    /**
     * Will be set to the system level error number if connection fails
     *
     * @var int $error_code
     */
    public $error_code;
    /**
     * Will be set to the system level error message if the connection fails.
     *
     * @var string $error_message
     */
    public $error_message;

    public function getRawCertificates()
    {
        return array_merge(
            [$this->params['options']['ssl']['peer_certificate']],
            $this->params['options']['ssl']['peer_certificate_chain'] ?? []
        );
    }

    /**
     * @return SslCertificate[]
     */
    public function getCertificates(): array
    {
        return array_unique(
            array_map(function ($certificate) {
                $Certificate = new SslCertificate();
                $Certificate->Certificate = openssl_x509_parse($certificate);
                $Certificate->remote_socket_name = $this->remote_socket_name;

                return $Certificate;
            }, $this->getRawCertificates())
        );
    }
}