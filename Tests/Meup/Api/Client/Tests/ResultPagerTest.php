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

use HomeApi\Client;
use HomeApi\Client\MeupApiClient;
use HomeApi\Client\ResultPager;
use HomeApi\Client\HttpClient\HttpClientInterface;

/**
 * ResultPagerTest.
 */
class ResultPagerTest extends \PHPUnit_Framework_TestCase
{
    private $version        = Client\ApiVersions::LATEST;
    private $clientId       = "1_meuptech";
    private $clientSecret   = "meuptech";

    /**
     * @test
     *
     * description fetchAll
     */
    public function shouldGetAllResults()
    {
        $firstJson  = file_get_contents(__DIR__ . "/Resources/orders_page_1.json");
        $secondJson = file_get_contents(__DIR__ . "/Resources/orders_page_2.json");
        $thirdJson  = file_get_contents(__DIR__ . "/Resources/orders_page_3.json");

        $amountLoops  = 3;
        $content      = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j');

        // httpClient mock
        $httpClientMock = $this->getHttpClientMock();
        // first call via Order:all
        $firstResponse  = $this->getResponseMock($firstJson);
        $httpClientMock
            ->expects($this->at(0))
            ->method('get')
            ->will($this->returnValue($firstResponse));
        $httpClientMock
            ->expects($this->at(0))
            ->method('getLastResponse')
            ->will($this->returnValue($firstResponse));

        // second call via ResultPager::fetchNext
        $secondResponse = $this->getResponseMock($secondJson);
        $httpClientMock
            ->expects($this->at(1))
            ->method('get')
            ->will($this->returnValue($secondResponse));
        $httpClientMock
            ->expects($this->at(2))
            ->method('getLastResponse')
            ->will($this->returnValue($secondResponse));

        // third call via ResultPager::fetchNext
        $thirdResponse  = $this->getResponseMock($thirdJson);
        $httpClientMock
            ->expects($this->at(3))
            ->method('get')
            ->will($this->returnValue($thirdResponse));
        $httpClientMock
            ->expects($this->at(4))
            ->method('getLastResponse')
            ->will($this->returnValue($thirdResponse));

        $clientMock = $this->getClientMock($httpClientMock);

        // memberApi Mock
        $orderApiMock = $this->getApiMock('HomeApi\Client\Api\Order');
        $orderApiMock
            ->expects($this->at(0))
            ->method('all')
            ->will($this->returnValue(json_decode($firstJson, true)));

        // Run fetchAll on result paginator
        $paginator = new ResultPager($clientMock);
        $result    = $paginator->fetchAll($orderApiMock, 'all', array());

        $this->assertEquals($amountLoops * count($content), count($result));
    }

    /**
     * @test
     *
     * description fetch
     */
    public function shouldGetSomeResults()
    {
        $pagination    = array('next' => '/api/orders?page=2');
        $firstJson     = file_get_contents(__DIR__ . "/Resources/orders_page_1.json");

        $responseMock = $this->getResponseMock($firstJson);
        $httpClient   = $this->getHttpClientMock($responseMock);
        $client       = $this->getClientMock($httpClient);

        $orderApiMock = $this->getApiMock('HomeApi\Client\Api\Order');
        $orderApiMock
            ->expects($this->once())
            ->method('all')
            ->will($this->returnValue($firstJson));

        $paginator = new ResultPager($client);
        $result    = $paginator->fetch($orderApiMock, 'all', array('HomeApi\Client'));

        $this->assertEquals($firstJson, $result);
        $paginationResult = $paginator->getPagination();
        $this->assertEquals($pagination['next'], $paginationResult['next']);
    }

    /**
     * @test
     *
     * description fetchNext
     */
    public function fetchNext()
    {
        $firstJson = file_get_contents(__DIR__ . "/Resources/orders_page_1.json");
        $secondJson = file_get_contents(__DIR__ . "/Resources/orders_page_2.json");

        $response1 = $this->getResponseMock($firstJson);
        $httpClient = $this->getHttpClientMock($response1);
        $httpClient
            ->expects($this->at(0))
            ->method('get')
            ->will($this->returnValue($response1));
        $response2 = $this->getResponseMock($secondJson);
        $httpClient
            ->expects($this->at(1))
            ->method('get')
            ->will($this->returnValue($response2));

        $client = $this->getClientMock($httpClient);

        $paginator = new ResultPager($client);
        $paginator->postFetch();

        $first = json_decode($firstJson, true);
        $next  = $paginator->fetchNext();

        $this->assertNotNull($next);
        $this->assertEquals(json_decode($secondJson, true), $next);
        $this->assertEquals($first['_links']['next']['href'], $next['_links']['self']['href']);
    }

    /**
     * @test
     *
     * description hasNext
     */
    public function shouldHaveNext()
    {
        $responseMock = $this->getResponseMock(file_get_contents(__DIR__.'/Resources/orders_page_1.json'));
        $httpClient   = $this->getHttpClientMock($responseMock);
        $client       = $this->getClientMock($httpClient);

        $paginator = new ResultPager($client);
        $paginator->postFetch();

        $this->assertEquals($paginator->hasNext(), true);
        $this->assertEquals($paginator->hasPrevious(), false);
    }

    /**
     * @test
     *
     * description hasPrevious
     */
    public function shouldHavePrevious()
    {
        $responseMock = $this->getResponseMock(file_get_contents(__DIR__.'/Resources/orders_page_3.json'));
        $httpClient   = $this->getHttpClientMock($responseMock);
        $client       = $this->getClientMock($httpClient);

        $paginator = new ResultPager($client);
        $paginator->postFetch();

        $this->assertEquals($paginator->hasPrevious(), true);
        $this->assertEquals($paginator->hasNext(), false);
    }

    /**
     * @test
     *
     * description hasPrevious
     */
    public function shouldHavePreviousAndNext()
    {
        $responseMock = $this->getResponseMock(file_get_contents(__DIR__.'/Resources/orders_page_2.json'));
        $httpClient   = $this->getHttpClientMock($responseMock);
        $client       = $this->getClientMock($httpClient);

        $paginator = new ResultPager($client);
        $paginator->postFetch();

        $this->assertEquals($paginator->hasPrevious(), true);
        $this->assertEquals($paginator->hasNext(), true);
    }

    protected function getResponseMock($body)
    {
        // response mock
        $responseMock = $this->getMock('Guzzle\Http\Message\Response', array(), array(200));
        $responseMock
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($body));

        return $responseMock;
    }

    protected function getClientMock(HttpClientInterface $httpClient = null)
    {
        // if no httpClient isset use the default HttpClient mock
        if (!$httpClient) {
            $httpClient = $this->getHttpClientMock();
        }

        $client = new MeupApiClient($this->clientId, $this->clientSecret, $this->version, $httpClient);
        $client->setHttpClient($httpClient);

        return $client;
    }

    protected function getHttpClientMock($responseMock = null)
    {
        // mock the client interface
        $clientInterfaceMock = $this->getMock('Guzzle\Http\Client', array('send'));
        $clientInterfaceMock
            ->expects($this->any())
            ->method('send');

        // create the httpClient mock
        $httpClientMock =
            $this->getMock('HomeApi\Client\HttpClient\HttpClient', array(), array(array(), $clientInterfaceMock));

        if ($responseMock) {
            $httpClientMock
                ->expects($this->any())
                ->method('getLastResponse')
                ->will($this->returnValue($responseMock));
        }

        return $httpClientMock;
    }

    protected function getApiMock($apiClass)
    {
        $client = $this->getClientMock();

        return $this->getMockBuilder($apiClass)
            ->setConstructorArgs(array($client))
            ->getMock();
    }
}
