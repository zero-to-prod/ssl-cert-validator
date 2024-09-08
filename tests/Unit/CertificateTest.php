<?php

namespace Tests\Unit;

use Tests\TestCase;
use Throwable;
use Zerotoprod\SslCertValidator\Certificate;
use Zerotoprod\SslCertValidator\Helpers\DataModel;
use Zerotoprod\StreamContext\DataModels\Context;
use Zerotoprod\StreamContext\DataModels\Options;
use Zerotoprod\StreamContext\DataModels\Ssl;

class CertificateTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider hosts
     *
     * @see          DataModel
     */
    public function validates_a_hostname(string $hostname, bool $expected): void
    {
        Context::new()
            ->set_Options(
                Options::new()->set_ssl(
                    Ssl::new()
                        ->set_peer_name('bogus')
                        ->set_verify_peer('bogus_site')
                )
            );

        $this->assertSame($expected, Certificate::isExpired($hostname));
    }

    public function hosts(): array
    {
        return [
            'https://badssl.com/' => ['https://badssl.com/', true],
            'badssl.com' => ['badssl.com', true],
            'https://expired.badssl.com/' => ['https://expired.badssl.com/', false],
            'expired.badssl.com' => ['expired.badssl.com', false],
        ];
    }

    /**
     * @test
     *
     * @throws Throwable
     * @see
     */
    public function wrong_host(): void
    {
        self::assertFalse(Certificate::hostIsValid('wrong.host.badssl.com'));
    }

    /**
     * @test
     *
     * @throws Throwable
     * @see
     */
    public function correct_host(): void
    {
        self::assertTrue(Certificate::hostIsValid('badssl.com'));
    }

    /**
     * @test
     *
     * @throws Throwable
     * @see
     */
    public function isSelfSigned(): void
    {
        self::assertTrue(Certificate::isSelfSigned('self-signed.badssl.com'));
    }

    /**
     * @test
     *
     * @throws Throwable
     * @see
     */
    public function isNotSelfSigned(): void
    {
        self::assertFalse(Certificate::isSelfSigned('badssl.com'));
    }

    /**
     * @test
     *
     * @throws Throwable
     * @see
     */
    public function hasUntrustedRoot(): void
    {
        self::assertFalse(
            Certificate::isTrustedRoot(
                'untrusted-root.badssl.com',
                '/usr/local/etc/ssl/certs/cacert.pem'
            )
        );
    }

    /**
     * @test
     *
     * @throws Throwable
     * @see
     */
    public function hasTrustedRoot(): void
    {
        self::assertTrue(
            Certificate::isTrustedRoot(
                'badssl.com',
                '/usr/local/etc/ssl/certs/cacert.pem'
            )
        );
    }
}