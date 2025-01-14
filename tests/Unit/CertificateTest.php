<?php

namespace Tests\Unit;

use Tests\TestCase;
use Throwable;
use Zerotoprod\SslCertValidator\SslCertificate;
use Zerotoprod\SslCertValidator\Helpers\DataModel;

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
        $this->assertSame($expected, SslCertificate::from($hostname)->isValid());
    }

    public static function hosts(): array
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
        self::assertFalse(SslCertificate::hostIsValid('wrong.host.badssl.com'));
    }

    /**
     * @test
     *
     * @throws Throwable
     * @see
     */
    public function correct_host(): void
    {
        self::assertTrue(SslCertificate::hostIsValid('badssl.com'));
    }

    /**
     * @test
     *
     * @throws Throwable
     * @see
     */
    public function isSelfSigned(): void
    {
        self::assertTrue(SslCertificate::isSelfSigned('self-signed.badssl.com'));
    }

    /**
     * @test
     *
     * @throws Throwable
     * @see
     */
    public function isNotSelfSigned(): void
    {
        self::assertFalse(SslCertificate::isSelfSigned('badssl.com'));
    }
}