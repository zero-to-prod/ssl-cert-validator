<?php

namespace Zerotoprod\SslCertValidator\DataModels;

use Zerotoprod\SslCertValidator\Helpers\DataModel;

/**
 * @link https://github.com/zero-to-prod/ssl-cert-validator
 */
class Certificate
{
    use DataModel;

    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public const name = 'name';
    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public const subject = 'subject';
    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public const hash = 'hash';
    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public const issuer = 'issuer';
    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public const version = 'version';
    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public const serialNumber = 'serialNumber';
    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public const serialNumberHex = 'serialNumberHex';
    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public const validFrom = 'validFrom';
    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public const validTo = 'validTo';
    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public const validFrom_time_t = 'validFrom_time_t';
    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public const validTo_time_t = 'validTo_time_t';
    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public const signatureTypeSN = 'signatureTypeSN';
    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public const signatureTypeLN = 'signatureTypeLN';
    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public const signatureTypeNID = 'signatureTypeNID';
    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public const purposes = 'purposes';
    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public const extensions = 'extensions';

    /**
     * @var string $name
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public $name;

    /**
     * @var array $subject
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public $subject;

    /**
     * @var string $hash
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public $hash;

    /**
     * @var array $issuer
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public $issuer;

    /**
     * @var int $version
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public $version;

    /**
     * @var string $serialNumber
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public $serialNumber;

    /**
     * @var string $serialNumberHex
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public $serialNumberHex;

    /**
     * @var string $validFrom
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public $validFrom;

    /**
     * @var string $validTo
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public $validTo;

    /**
     * @var int $validFrom_time_t
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public $validFrom_time_t;

    /**
     * @var int $validTo_time_t
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public $validTo_time_t;

    /**
     * @var string $signatureTypeSN
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public $signatureTypeSN;

    /**
     * @var string $signatureTypeLN
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public $signatureTypeLN;

    /**
     * @var int $signatureTypeNID
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public $signatureTypeNID;

    /**
     * @var array $purposes
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public $purposes;

    /**
     * @var array $extensions
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public $extensions;

    /**
     * Checks if an SSL certificate is expired.
     *
     * Example:
     * ```
     * Certificate::from('https://badssl.com/')->isExpired(time())
     * Certificate::from('badssl.com')->isExpired()
     * Certificate::from('badssl.com:999')->isExpired()
     * ```
     *
     * @param  ?int  $time  (Optional) Timestamp to check against.
     *
     * @return bool  True if the certificate is valid, false if expired.
     *
     *
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public function isValid(?int $time = null): bool
    {
        if (!$time) {
            $time = time();
        }

        return !($time < $this->validFromDate() || $time > $this->expirationDate());
    }

    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public function validFromDate(): int
    {
        return $this->validFrom_time_t;
    }

    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public function expirationDate(): int
    {
        return $this->validTo_time_t;
    }

    /**
     * @link https://github.com/zero-to-prod/ssl-cert-validator
     */
    public function daysUntilExpirationDate(): int
    {
        return ($this->validTo_time_t - $this->validFrom_time_t) / 86400;
    }
}