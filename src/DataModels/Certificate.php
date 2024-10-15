<?php

namespace Zerotoprod\SslCertValidator\DataModels;

use Zerotoprod\SslCertValidator\Helpers\DataModel;

/**
 * @link https://github.com/zero-to-prod/ssl-cert-validator
 */
class Certificate
{
    use DataModel;

    public const name = 'name';
    public const subject = 'subject';
    public const hash = 'hash';
    public const issuer = 'issuer';
    public const version = 'version';
    public const serialNumber = 'serialNumber';
    public const serialNumberHex = 'serialNumberHex';
    public const validFrom = 'validFrom';
    public const validTo = 'validTo';
    public const validFrom_time_t = 'validFrom_time_t';
    public const validTo_time_t = 'validTo_time_t';
    public const signatureTypeSN = 'signatureTypeSN';
    public const signatureTypeLN = 'signatureTypeLN';
    public const signatureTypeNID = 'signatureTypeNID';
    public const purposes = 'purposes';
    public const extensions = 'extensions';

    /** @var string $name */
    public $name;

    /** @var array $subject */
    public $subject;

    /** @var string $hash */
    public $hash;

    /** @var array $issuer */
    public $issuer;

    /** @var int $version */
    public $version;

    /** @var string $serialNumber */
    public $serialNumber;

    /** @var string $serialNumberHex */
    public $serialNumberHex;

    /** @var string $validFrom */
    public $validFrom;

    /** @var string $validTo */
    public $validTo;

    /** @var int $validFrom_time_t */
    public $validFrom_time_t;

    /** @var int $validTo_time_t */
    public $validTo_time_t;

    /** @var string $signatureTypeSN */
    public $signatureTypeSN;

    /** @var string $signatureTypeLN */
    public $signatureTypeLN;

    /** @var int $signatureTypeNID */
    public $signatureTypeNID;

    /** @var array $purposes */
    public $purposes;

    /** @var array $extensions */
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
     * @see https://github.com/zero-to-prod/ssl-cert-validator
     */
    public function isValid(?int $time = null): bool
    {
        if (!$time) {
            $time = time();
        }

        return !($time < $this->validFromDate() || $time > $this->expirationDate());
    }

    public function validFromDate(): int
    {
        return $this->validFrom_time_t;
    }

    public function expirationDate(): int
    {
        return $this->validTo_time_t;
    }

    public function daysUntilExpirationDate(): int
    {
        return ($this->validTo_time_t - $this->validFrom_time_t) / 86400;
    }
}