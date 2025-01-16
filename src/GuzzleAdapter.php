<?php

namespace Minhyung\KoreaDays;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Utils;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GuzzleAdapter implements ClientInterface
{
    private GuzzleClient $guzzle;

    /**
     * GuzzleAdapter constructor.
     * 
     * @param  array<string, mixed>  $config
     * @return void
     */
    public function __construct(array $config = [])
    {
        $stack = new HandlerStack(Utils::chooseHandler());
        $stack->push(Middleware::prepareBody(), 'prepare_body');

        $config = array_merge(['handler' => $stack], $config);

        $this->guzzle = new GuzzleClient($config);
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->guzzle->send($request);
    }
}
