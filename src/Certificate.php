<?php

namespace Zerotoprod\SslCertValidator;

use Throwable;
use Zerotoprod\SocketClient\DataModels\SocketClientArgs;
use Zerotoprod\SocketClient\StreamSocket;
use Zerotoprod\SslCertValidator\DataModels\SocketParams;
use Zerotoprod\SslCertValidator\DataModels\SslCertificate;
use Zerotoprod\SslCertValidator\DataModels\Url;
use Zerotoprod\StreamContext\DataModels\Options;
use Zerotoprod\StreamContext\DataModels\Ssl;
use Zerotoprod\StreamContext\StreamContext;

class Certificate
{

    public static function fromHostName(string $hostname): SslCertificate
    {
        try {
            $Url = Url::parse($hostname);
            $client = StreamSocket::client(
                SocketClientArgs::new()
                    ->set_address('ssl://'.$Url->host.':'.($Url->port ?: 443))
                    ->set_timeout(30)
                    ->set_flags(STREAM_CLIENT_CONNECT)
                    ->set_context(
                        StreamContext::from()
                            ->set_Options(
                                Options::new()
                                    ->set_ssl(
                                        Ssl::new()
                                            ->set_capture_peer_cert(true)
                                            ->set_peer_name($Url->host)
                                    )
                            )
                            ->create()
                    )
            );
            $params = $client->getParams();
            $client->close();

            return SocketParams::new()->set_params($params)->getCertificates()[0];
        } catch (Throwable $exception) {
            return SslCertificate::new()->set_error($exception->getMessage());
        }
    }

    /**
     * @param  string  $hostname
     * @param  ?int    $time
     *
     * @return bool
     */
    public static function validate(string $hostname, int $time = null): bool
    {
        if (!$time) {
            $time = time();
        }
        $SslCertificate = self::fromHostName($hostname);

        return !$SslCertificate->error && $SslCertificate->isValid($time);
    }
}