<?php

use Carbon\Carbon;
use Arkade\Bronto\RestAuthentication;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use PHPUnit\Framework\TestCase;

class AuthenticationTest extends TestCase
{

    public function testAuthUrlSetterGetter()
    {
        $auth = new RestAuthentication;
        $auth->setAuthUrl('https://auth.bronto.com/oauth2/token');
        $this->assertEquals('https://auth.bronto.com/oauth2/token', $auth->getAuthUrl());
    }

    public function testClientIdGetterSetter()
    {
        $auth = new RestAuthentication;
        $auth->setClientId('arkade');
        $this->assertEquals('arkade', $auth->getClientId());
    }

    public function testPasswordGetterSetter()
    {
        $auth = new RestAuthentication;
        $auth->setClientSecret('secret');
        $this->assertEquals('secret', $auth->getClientSecret());
    }

    public function testEndpointGetterSetter()
    {
        $auth = new RestAuthentication;
        $auth->setEndpoint('https://rest.bronto.com');
        $this->assertEquals('https://rest.bronto.com', $auth->getEndpoint());
    }

    public function testTokenExpiryGetterSetter()
    {
        $auth = new RestAuthentication;
        $auth->setTokenExpiry(Carbon::createFromTimestamp('3600'));
        $this->assertEquals(Carbon::createFromTimestamp('3600'),
            $auth->getTokenExpiry());
    }

    public function testTokenIsExpired()
    {
        $auth = new RestAuthentication;

        $auth->setTokenExpiry(Carbon::now()->addHour());
        $this->assertEquals(false, $auth->isTokenExpired());

        $auth->setTokenExpiry(Carbon::now()->subHour());
        $this->assertEquals(true, $auth->isTokenExpired());
    }


    public function testTokenSetter()
    {
        $auth = new RestAuthentication;
        $auth->setToken('mytoken');
        $auth->setTokenExpiry(Carbon::now()->addHour());
        $this->assertEquals('mytoken', $auth->getToken());
    }

    public function testTokenGetter()
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
        $this->assertEquals('aRandomToken', $auth->getToken());
        $this->assertEquals('aRandomRefreshToken', $auth->getRefreshToken());
        $this->assertEquals(Carbon::now()->addSeconds(3600)->toDateTimeString(), $auth->getTokenExpiry()->toDateTimeString());
    }


    public function testCreateToken(){
        // Set up our request(s) history
        $container = [];
        $history = Middleware::history($container);

        // Set up our mock handler for the auth response(s)
        $mock = new MockHandler(
            [
                new Response(
                    200, [], json_encode(
                        [
                            'access_token' => 'aRandomToken',
                            'refresh_token' => 'aRandomRefreshToken',
                            'expires_in'  => 3600,
                        ]
                    )
                ),
            ]
        );

        // Set up our Guzzle Handler Stack History (for requests)
        $stack = HandlerStack::create($mock);
        $stack->push($history);

        // Let's make the client with our mock responses and history container
        $auth = new RestAuthentication($stack);
        $auth->setAuthUrl('https://auth.bronto.com/oauth2/token');
        $auth->setClientId('arkade');
        $auth->setClientSecret('secret');

        // Let's call create token
        $auth->createOauthToken();

        // Check that only one call was made to get q new token
        $this->assertCount(1, $container);

        // Check that it was a POST request
        $this->assertEquals('POST', $container[0]['request']->getMethod());

        // Ensure that it went to the Auth URL specified
        $this->assertEquals('/oauth2/token',
            $container[0]['request']->getRequestTarget());

        // Lastly, make sure the body has the correct form params
        $this->assertEquals(
            "grant_type=client_credentials&client_id=arkade&client_secret=secret",
            (string)$container[0]['request']->getBody()
        );

        $this->assertEquals('aRandomToken', $auth->getToken());
        $this->assertEquals('aRandomRefreshToken', $auth->getRefreshToken());
    }

    public function testRefreshToken(){
        // Set up our request(s) history
        $container = [];
        $history = Middleware::history($container);

        // Set up our mock handler for the auth response(s)
        $mock = new MockHandler(
            [
                new Response(
                    200, [], json_encode(
                        [
                            'access_token' => 'aRandomToken',
                            'refresh_token' => 'anotherRandomRefreshToken',
                            'expires_in'  => 3600,
                        ]
                    )
                )
            ]
        );

        // Set up our Guzzle Handler Stack History (for requests)
        $stack = HandlerStack::create($mock);
        $stack->push($history);

        // Let's make the client with our mock responses and history container
        $auth = new RestAuthentication($stack);
        $auth->setAuthUrl('https://auth.bronto.com/oauth2/token');
        $auth->setClientId('arkade');
        $auth->setClientSecret('secret');
        $auth->setRefreshToken('aRandomRefreshToken');

        // Let's call create token
        $auth->refreshOauthToken();

        // Check that only one call was made to get q new token
        $this->assertCount(1, $container);

        // Check that it was a POST request
        $this->assertEquals('POST', $container[0]['request']->getMethod());

        // Ensure that it went to the Auth URL specified in the setClientConfig
        $this->assertEquals('/oauth2/token',
            $container[0]['request']->getRequestTarget());

        // Lastly, make sure the body has the correct form params
        $this->assertEquals(
            "grant_type=refresh_token&client_id=arkade&client_secret=secret&refresh_token=aRandomRefreshToken",
            (string)$container[0]['request']->getBody()
        );

        $this->assertEquals('aRandomToken', $auth->getToken());
        $this->assertEquals('anotherRandomRefreshToken', $auth->getRefreshToken());

    }

}