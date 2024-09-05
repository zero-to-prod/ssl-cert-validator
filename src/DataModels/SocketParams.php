<?php

namespace Zerotoprod\SslCertValidator\DataModels;

use Zerotoprod\SslCertValidator\Helpers\DataModel;

/**
 * @internal
 * @method self set_params($params) Params from `stream_context_get_params()`.
 */
class SocketParams
{
    use DataModel;

    public const params = 'params';

    public $params;

    /**
     * @internal
     * @see https://github.com/zero-to-prod/ssl-cert-validator
     */
    public function getRawCertificates(): array
    {
        return array_merge(
            [$this->params['options']['ssl']['peer_certificate']],
            $this->params['options']['ssl']['peer_certificate_chain'] ?? []
        );
    }

    /**
     * @return SslCertificate[]
     *
     * @internal
     * @see https://github.com/zero-to-prod/ssl-cert-validator
     */
    public function getCertificates(): array
    {
        return array_unique(
            array_map(static function ($certificate) {
                $Certificate = new SslCertificate();
                $Certificate->Certificate = openssl_x509_parse($certificate);

                return $Certificate;
            }, $this->getRawCertificates())
        );
    }
}