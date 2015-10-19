<?php
/**
 * This file is part of the PHP Client for 1001 Pharmacies API.
 *
 * (c) florianajir <https://github.com/florianajir/home-api>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HomeApi\Client\Tests;

use HomeApi\Client\Api;
use HomeApi\Client\ApiVersions;
use HomeApi\Client\HttpClient\HttpClient;
use HomeApi\Client\MeupApiClient;
use HomeApi\Client\Exception\InvalidArgumentException;
use HomeApi\Client\Exception\BadMethodCallException;

/**
 * Class MeupClientTest
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class MeupClientTest extends \PHPUnit_Framework_TestCase
{
    private $version        = ApiVersions::LATEST;
    private $clientId       = "1_meuptech";
    private $clientSecret   = "meuptech";

    /**
     * @test
     */
    public function shouldNotHaveToInjectHttpClientToConstructor()
    {
        $client = new MeupApiClient($this->clientId, $this->clientSecret, $this->version);

        $this->assertInstanceOf('HomeApi\Client\HttpClient\HttpClient', $client->getHttpClient());
    }

    /**
     * @test
     */
    public function shouldInjectHttpClientInterfaceToConstructor()
    {
        $client = new MeupApiClient($this->clientId, $this->clientSecret, $this->version, $this->getHttpClientMock());

        $this->assertInstanceOf('HomeApi\Client\HttpClient\HttpClientInterface', $client->getHttpClient());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldThrowExceptionWhenCredentialsMissing()
    {
        $httpClient = $this->getHttpClientMock(array('addListener'));

        new MeupApiClient(null, null, $this->version, $httpClient);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldThrowExceptionWhenInvalidApiVersion()
    {
        $httpClient = $this->getHttpClientMock(array('addListener'));

        new MeupApiClient($this->clientId, $this->clientSecret, 'badVersion', $httpClient);
    }

    /**
     * @test
     */
    public function shouldClearHeadersLazy()
    {
        $httpClient = $this->getHttpClientMock(array('clearHeaders'));
        $httpClient->expects($this->once())->method('clearHeaders');

        $client = new MeupApiClient($this->clientId, $this->clientSecret, $this->version, $httpClient);
        $client->clearHeaders();
    }

    /**
     * @test
     */
    public function shouldSetHeadersLazily()
    {
        $headers = array('header_1', 'header_2');

        $httpClient = $this->getHttpClientMock();
        $httpClient->expects($this->once())->method('setHeaders')->with($headers);

        $client = new MeupApiClient($this->clientId, $this->clientSecret, $this->version, $httpClient);
        $client->setHeaders($headers);
    }

    /**
     * @test
     */
    public function shouldSetHttpClientLazily()
    {
        $httpClient = $this->getHttpClientMock();

        $client = new MeupApiClient($this->clientId, $this->clientSecret, $this->version, $httpClient);
        $client->setHttpClient($httpClient);
    }

    /**
     * @test
     * @dataProvider getApiClassesProvider
     *
     * @param $apiName
     * @param $class
     */
    public function shouldGetApiInstance($apiName, $class)
    {
        $client = new MeupApiClient($this->clientId, $this->clientSecret, $this->version);

        $this->assertInstanceOf($class, $client->api($apiName));
    }

    /**
     * @test
     * @dataProvider getApiClassesProvider
     *
     * @param $apiName
     * @param $class
     */
    public function shouldGetMagicApiInstance($apiName, $class)
    {
        $client = new MeupApiClient($this->clientId, $this->clientSecret, $this->version);

        $this->assertInstanceOf($class, $client->$apiName());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldNotGetApiInstance()
    {
        $client = new MeupApiClient($this->clientId, $this->clientSecret, $this->version);
        $client->api('api_manager_not_found');
    }

    /**
     * @test
     * @expectedException BadMethodCallException
     */
    public function shouldNotGetMagicApiInstance()
    {
        $client = new MeupApiClient($this->clientId, $this->clientSecret, $this->version);

        $notFoundApi = 'doNotExistApiManager';
        $client->{$notFoundApi}();
    }

    public function getApiClassesProvider()
    {
        return array(
            array(Api::ORDERS, 'HomeApi\Client\Api\Order'),
        );
    }

    /**
     * @param array $methods
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|HttpClient
     */
    public function getHttpClientMock(array $methods = array())
    {
        $methods = array_merge(
            array('get', 'post', 'patch', 'put', 'delete', 'request', 'setOption', 'setHeaders', 'authenticate'),
            $methods
        );

        return $this->getMock('HomeApi\Client\HttpClient\HttpClientInterface', $methods);
    }

    /**
     * @test
     */
    public function shouldGetApiVersionList()
    {
        $httpClient = $this->getHttpClientMock();
        $client = new MeupApiClient($this->clientId, $this->clientSecret, $this->version, $httpClient);

        $this->assertContains('latest', $client->getSupportedApiVersions());
    }

    /**
     * @test
     */
    public function shouldGetApiList()
    {
        $httpClient = $this->getHttpClientMock();
        $client = new MeupApiClient($this->clientId, $this->clientSecret, $this->version, $httpClient);

        $this->assertContains('order', $client->getApiList());
        $this->assertContains('brand', $client->getApiList());
        $this->assertContains('reason', $client->getApiList());
        $this->assertContains('product', $client->getApiList());
    }
}
