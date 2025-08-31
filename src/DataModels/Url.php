<?php

namespace Zerotoprod\SslCertValidator\DataModels;

use Zerotoprod\Url\Parsable;

/**
 * @link https://github.com/zero-to-prod/ssl-cert-validator
 * Fetch, validate, and verify SSL certificates.
 */
class Url extends \Zerotoprod\Url\Url
{
    use Parsable;
}