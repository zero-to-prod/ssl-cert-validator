<?php

namespace Zerotoprod\SslCertValidator\DataModels;

use Zerotoprod\SslCertValidator\Helpers\DataModel;

/**
 * @internal
 *
 * @method self setName(string $name)
 * @method self setSubject(array $subject)
 * @method self setHash(string $hash)
 * @method self setIssuer(array $issuer)
 * @method self setVersion(int $version)
 * @method self setSerialNumber(string $serialNumber)
 * @method self setSerialNumberHex(string $serialNumberHex)
 * @method self setValidFrom(string $validFrom)
 * @method self setValidTo(string $validTo)
 * @method self setValidFromTimeT(int $validFromTimeT)
 * @method self setValidToTimeT(int $validToTimeT)
 * @method self setSignatureTypeSN(string $signatureTypeSN)
 * @method self setSignatureTypeLN(string $signatureTypeLN)
 * @method self setSignatureTypeNID(int $signatureTypeNID)
 * @method self setPurposes(array $purposes)
 * @method self setExtensions(array $extensions)
 *
 * @see https://github.com/zero-to-prod/ssl-cert-validator
 */
class SslCertificate
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

    public function isValid(int $time): bool
    {
        return !($time < $this->validFromTime() || $time > $this->validToTime());
    }

    public function validFromTime(): int
    {
        return $this->validFrom_time_t;
    }

    public function validToTime(): int
    {
        return $this->validTo_time_t;
    }
}