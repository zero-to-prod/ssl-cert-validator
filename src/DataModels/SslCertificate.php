<?php

namespace Zerotoprod\SslCertValidator\DataModels;

use Zerotoprod\SslCertValidator\Helpers\DataModel;

/**
 * @internal
 * @method self set_Certificate($params)
 * @method self set_error(string $error) Any error associated with the SslCertificate.
 */
class SslCertificate
{
    use DataModel;

    public const Certificate = 'Certificate';
    public const error = 'error';

    /** @var array|false $Certificate */
    public $Certificate;

    public $error;

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
}