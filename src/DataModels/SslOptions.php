<?php

namespace Zerotoprod\SslCertValidator\DataModels;

use Zerotoprod\DataModel\DataModel;

class SslOptions
{
    use DataModel;
    public const capture_peer_cert = 'capture_peer_cert';
    public const capture_peer_cert_chain = 'capture_peer_cert_chain';
    public const SNI_enabled = 'SNI_enabled';
    public const peer_name = 'peer_name';
    public const verify_peer = 'verify_peer';
    public const verify_peer_name = 'verify_peer_name';
    public const follow_location = 'follow_location';

    /** @var bool $capture_peer_cert */
    public $capture_peer_cert;
    /** @var bool $capture_peer_cert_chain */
    public $capture_peer_cert_chain;
    /** @var bool $SNI_enabled */
    public $SNI_enabled;
    /** @var string $peer_name */
    public $peer_name;
    /** @var bool $verify_peer */
    public $verify_peer;
    /** @var bool $verify_peer_name */
    public $verify_peer_name;
    /** @var int $follow_location */
    public $follow_location;
}