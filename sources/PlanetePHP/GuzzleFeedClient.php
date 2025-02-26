<?php

declare(strict_types=1);

namespace PlanetePHP;

use GuzzleHttp\Client;
use Laminas\Feed\Reader\Http\ClientInterface;
use Laminas\Feed\Reader\Http\HeaderAwareClientInterface;
use Laminas\Feed\Reader\Http\Psr7ResponseDecorator;
use Laminas\Feed\Reader\Http\ResponseInterface;

final class GuzzleFeedClient implements HeaderAwareClientInterface
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function get($uri, array $headers = []): ResponseInterface
    {
        return new Psr7ResponseDecorator($this->client->request('GET', $uri, [
            'headers' => $headers,
        ]));
    }
}
