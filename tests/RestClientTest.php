<?php

use Carbon\Carbon;
use Arkade\Bronto\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use Arkade\Bronto\RestAuthentication;
use PHPUnit\Framework\TestCase;

class RestClientTest extends TestCase
{
    public function testRequest()
    {
        $auth = new RestAuthentication(
            HandlerStack::create(new MockHandler([
                new Response(
                    200, [], json_encode(
                        [
                            'access_token' => 'aRandomToken',
                            'refresh_token' => 'aRandomRefreshToken',
                            'expires_in'  => 3600,
                        ]
                    )
                ),
            ]))
        );

        $auth->setAuthUrl('https://auth.bronto.com/oauth2/token');
        $auth->setClientId('arkade');
        $auth->setClientSecret('secret');
        $auth->setEndpoint('https://rest.bronto.com');

        // Set up our request(s) history
        $container = [];
        $history = Middleware::history($container);

        // Set up our mock handler for the auth response(s)
        $mock = new MockHandler(
            [new Response(200, [], json_encode([]))]
        );

        // Set up our Guzzle Handler Stack History (for requests)
        $stack = HandlerStack::create($mock);
        $stack->push($history);

        // Let's make the client with our mock responses and history container
        $client = new Client($auth, $stack);

        // Let's test!
        $client->request('GET', 'some-endpoint',
            ['query' => ['foo' => 'bar']]);

        // Check the correct outgoing call was made
        $this->assertEquals('GET', $container[0]['request']->getMethod());
        $this->assertEquals('/some-endpoint?foo=bar',
            $container[0]['request']->getRequestTarget());

    }
}