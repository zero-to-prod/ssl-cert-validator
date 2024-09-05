<?php

namespace Tests\Unit;

use Tests\TestCase;
use Zerotoprod\SslCertValidator\Certificate;
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
        $this->assertSame($expected, Certificate::validate($hostname));
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
}