<?php

namespace Zerotoprod\SslCertValidator\Helpers;

use Zerotoprod\SslCertValidator\DataModels\SocketClient;
use Zerotoprod\SslCertValidator\DataModels\SocketClientRequest;
use Zerotoprod\SslCertValidator\DataModels\SslOptions;
use Zerotoprod\SslCertValidator\DataModels\StreamSocketClientResponse;

class StreamSocket
{
    public static function client(SocketClientRequest $SocketClientRequest): SocketClient
    {
        $SocketClientResponse = self::streamClient($SocketClientRequest);

        $SocketClient = SocketClient::from(
            array_merge([
                SocketClient::client => $SocketClientResponse->client,
                SocketClient::remote_socket_name => $SocketClientResponse->remoteSocketName(),
                SocketClient::params => $SocketClientResponse->params(),
            ],
                [$SocketClientRequest->toArray()]
            )
        );

        fclose($SocketClientResponse->client);

        return $SocketClient;
    }

    public static function streamClient(SocketClientRequest $StreamSocketClientRequest): StreamSocketClientResponse
    {
        return StreamSocketClientResponse::from([
            StreamSocketClientResponse::client => stream_socket_client(
                $StreamSocketClientRequest->sslAddress(),
                $error_code,
                $error_message,
                $StreamSocketClientRequest->timeout,
                $StreamSocketClientRequest->flags,
                stream_context_create([
                    'ssl' =>
                        array_merge(
                            $StreamSocketClientRequest->SslOptions->toArray(),
                            [SslOptions::peer_name => $StreamSocketClientRequest->Url->host]
                        )
                ])
            ),
            StreamSocketClientResponse::error_code => $error_code,
            StreamSocketClientResponse::error_message => $error_message,
        ]);
    }
}