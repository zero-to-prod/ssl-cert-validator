<?php

namespace Tests\Unit;

use Tests\TestCase;
use Zerotoprod\DataModel\DataModel;
use Zerotoprod\SslCertValidator\SslCertificate;

class SmokeTest extends TestCase
{
    /**
     * @test
     *
     * @see DataModel
     */
    public function creates_instance_from_array(): void
    {
        $this->assertFalse(SslCertificate::hostIsValid('https://interior-gardens.com/', time()));
        $this->assertTrue(SslCertificate::hostIsValid('https://google.com/', time()));
    }
}