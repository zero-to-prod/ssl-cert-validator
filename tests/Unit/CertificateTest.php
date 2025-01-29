<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zerotoprod\SslCertValidator\SslCertificate;

class CertificateTest extends TestCase
{
    #[DataProvider('hosts')]
    #[Test] public function validates_a_hostname(string $hostname, bool $expected): void
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

    #[Test] public function wrong_host(): void
    {
        self::assertFalse(SslCertificate::hostIsValid('wrong.host.badssl.com'));
    }

    #[Test] public function correct_host(): void
    {
        self::assertTrue(SslCertificate::hostIsValid('badssl.com'));
    }

    #[Test] public function isSelfSigned(): void
    {
        self::assertTrue(SslCertificate::isSelfSigned('self-signed.badssl.com'));
    }

    #[Test] public function isNotSelfSigned(): void
    {
        self::assertFalse(SslCertificate::isSelfSigned('badssl.com'));
    }
}