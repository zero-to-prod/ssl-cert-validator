<?php

namespace Zerotoprod\SslCertValidator\DataModels;

use Zerotoprod\DataModel\DataModel;

class SocketClientRequest
{
    use DataModel;

    public const Url = 'Url';
    public const timeout = 'timeout';
    public const flags = 'flags';
    public const SslOptions = 'SslOptions';

    /** @var \Zerotoprod\Url\Url $Url */
    public $Url;
    /** @var float $timeout */
    public $timeout;
    /** @var int $flags */
    public $flags;
    /** @var SslOptions $SslOptions */
    public $SslOptions;

    public function sslAddress(): string
    {
        $port = $this->Url->port ?? 443;

        return "ssl://{$this->Url->host}:$port";
    }
}