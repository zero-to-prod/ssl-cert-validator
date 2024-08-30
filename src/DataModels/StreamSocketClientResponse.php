<?php

namespace Zerotoprod\SslCertValidator\DataModels;

use Zerotoprod\DataModel\DataModel;

class StreamSocketClientResponse
{
    use DataModel;

    public const client = 'client';
    public const error_code = 'error_code';
    public const error_message = 'error_message';

    /**
     * Open Internet or Unix domain socket connection
     *
     * @var false|resource $client
     */
    public $client;
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

    /**
     * The remote socket to get the name of.
     * A wrapper around `stream_socket_get_name()`
     *
     * @return false|string The name of the socket or false on error.
     */
    public function remoteSocketName()
    {
        return stream_socket_get_name($this->client, true);
    }

    /**
     * The local socket to get the name of.
     * A wrapper around `stream_socket_get_name()`
     *
     * @return false|string The name of the socket or false on error.
     */
    public function localSocketName()
    {
        return stream_socket_get_name($this->client, false);
    }

    /**
     * Retrieves parameters from a context
     * A wrapper around `stream_context_get_params()`
     *
     * @return array an associate array containing all context options and parameters.
     */
    public function params(): array
    {
        return stream_context_get_params($this->client);
    }
}