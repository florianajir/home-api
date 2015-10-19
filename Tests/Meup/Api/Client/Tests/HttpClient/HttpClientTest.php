<?php
/**
 * This file is part of the PHP Client for 1001 Pharmacies API.
 *
 * (c) florianajir <https://github.com/florianajir/home-api>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HomeApi\Client\Tests\HttpClient;

use HomeApi\Client\HttpClient\HttpClient;
use HomeApi\Client\HttpClient\Message\ResponseParser;
use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Http\Client as GuzzleClient;

/**
 * Class HttpClientTest
 * @package HomeApi\Client\Tests\HttpClient
 */
class HttpClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeAbleToPassOptionsToConstructor()
    {
        $httpClient = new TestHttpClient('1_meuptech', 'meuptech', array(
            'timeout' => 33
        ), $this->getBrowserMock());

        $this->assertEquals(33, $httpClient->getOption('timeout'));
        $this->assertEquals(5000, $httpClient->getOption('api_limit'));
    }

    /**
     * @test
     */
    public function shouldBeAbleToSetOption()
    {
        $httpClient = new TestHttpClient(array(), $this->getBrowserMock());
        $httpClient->setOption('timeout', 666);

        $this->assertEquals(666, $httpClient->getOption('timeout'));
    }

    public function getAuthenticationFullData()
    {
        return array(
            array('client_id', 'client_secret'),
        );
    }

    /**
     * @test
     */
    public function shouldDoGETRequest()
    {
        $path       = '/some/path';
        $parameters = array('a' => 'b');
        $headers    = array('c' => 'd');

        $client = $this->getBrowserMock();

        $httpClient = new HttpClient('1_meuptech', 'meuptech', array(), $client);
        $httpClient->get($path, $parameters, $headers);
    }

    /**
     * @test
     * @Todo find a way to intercept and disable authentication request
     */
//    public function shouldDoPOSTRequest()
//    {
//        $path       = '/some/path';
//        $body       = 'a = b';
//        $headers    = array('c' => 'd');
//
//        $client = $this->getBrowserMock();
//        $client
//            ->expects($this->once())
//            ->method('createRequest')
//            ->with('POST', $path, $this->isType('array'), $body);
//
//        $httpClient = new HttpClient('1_meuptech', 'meuptech', array(), $client);
//        $httpClient->post($path, $body, $headers);
//    }

    /**
     * @test
     * @Todo find a way to intercept and disable authentication request
     */
//    public function shouldDoPOSTRequestWithoutContent()
//    {
//        $path       = '/some/path';
//
//        $client = $this->getBrowserMock();
//        $client
//            ->expects($this->once())
//            ->method('createRequest')
//            ->with('POST', $path, $this->isType('array'));
//
//        $httpClient = new HttpClient('1_meuptech', 'meuptech', array(), $client);
//        $httpClient->post($path);
//    }

    /**
     * @test
     */
    public function shouldDoPATCHRequest()
    {
        $path       = '/some/path';
        $body       = 'a = b';
        $headers    = array('c' => 'd');

        $client = $this->getBrowserMock();

        $httpClient = new HttpClient('1_meuptech', 'meuptech', array(), $client);
        $httpClient->patch($path, $body, $headers);
    }

    /**
     * @test
     */
    public function shouldDoDELETERequest()
    {
        $path       = '/some/path';
        $body       = 'a = b';
        $headers    = array('c' => 'd');

        $client = $this->getBrowserMock();

        $httpClient = new HttpClient('1_meuptech', 'meuptech', array(), $client);
        $httpClient->delete($path, $body, $headers);
    }

    /**
     * @test
     */
    public function shouldDoPUTRequest()
    {
        $path       = '/some/path';
        $headers    = array('c' => 'd');

        $client = $this->getBrowserMock();

        $httpClient = new HttpClient('1_meuptech', 'meuptech', array(), $client);
        $httpClient->put($path, $headers);
    }

    /**
     * @test
     */
    public function shouldDoCustomRequest()
    {
        $path       = '/some/path';
        $body       = 'a = b';
        $options    = array('c' => 'd');

        $client = $this->getBrowserMock();

        $httpClient = new HttpClient('1_meuptech', 'meuptech', array(), $client);
        $httpClient->request($path, $body, 'HEAD', $options);
    }

    /**
     * @test
     */
    public function shouldAllowToReturnRawContent()
    {
        $path       = '/some/path';
        $parameters = array('a = b');
        $headers    = array('c' => 'd');

        $message = $this->getMock('Guzzle\Http\Message\Response', array(), array(200));
        $message->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue('Just raw context'));

        $client = $this->getBrowserMock();
        $client->expects($this->once())
            ->method('send')
            ->will($this->returnValue($message));

        $httpClient = new TestHttpClient('1_meuptech', 'meuptech', array(), $client);
        $response = $httpClient->get($path, $parameters, $headers);

        $this->assertEquals("Just raw context", $response->getBody());
        $this->assertInstanceOf('Guzzle\Http\Message\MessageInterface', $response);
    }

    protected function getBrowserMock(array $methods = array())
    {
        $mock = $this->getMock(
            '\Guzzle\Http\Client',
            array_merge(
                array('send', 'createRequest'),
                $methods
            )
        );

        // Response from API Authentication
        $responseMock = $this->getMock('\Guzzle\Http\Message\Response', array(), array(200, null, '{}'));
        $responseMock
            ->expects($this->any())
            ->method('json')
            ->willReturn(array('access_token'=>"123456"))
        ;
        $requestMock = $this->getMock('\Guzzle\Http\Message\Request', array(), array('GET', 'some'));
        $requestMock
            ->expects($this->any())
            ->method('send')
            ->will($this->returnValue($responseMock));

        $mock
            ->expects($this->any())
            ->method('createRequest')
            ->will($this->returnValue($requestMock))
        ;

        return $mock;
    }
}

/**
 * Class TestHttpClient
 *
 * @todo Refactor this ****
 */
class TestHttpClient extends HttpClient
{
    public function getOption($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    /**
     * @param string $path
     * @param null   $body
     * @param string $httpMethod
     * @param array  $headers
     * @param array  $options
     *
     * @return array|Response|null
     */
    public function request($path, $body, $httpMethod = 'GET', array $headers = array(), array $options = array())
    {
        $request = $this->client->createRequest($httpMethod, $path);

        return $this->client->send($request);
    }
}
