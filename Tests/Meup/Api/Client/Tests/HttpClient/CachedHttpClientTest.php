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

use HomeApi\Client\HttpClient\CachedHttpClient;
use Guzzle\Http\Message\Response;

/**
 * Class CachedHttpClientTest
 * @package HomeApi\Client\Tests\HttpClient
 */
class CachedHttpClientTest extends HttpClientTest
{
    /**
     * @test
     */
    public function shouldCacheResponseAtFirstTime()
    {
        $cache = $this->getCacheMock();
        $response = new Response(200);

        $client = $this->getBrowserMock();
        $client->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));

        $httpClient = new CachedHttpClient('1_meuptech', 'meuptech', array('base_url' => ''), $client);
        $httpClient->setCache($cache);

        $cache->expects($this->once())->method('set')->with('test', $response);
        $httpClient->get('test');
    }

    /**
     * @test
     */
    public function shouldGetCachedResponseWhileResourceNotModified()
    {
        $cache = $this->getCacheMock();
        $response = new Response(304);

        $client = $this->getBrowserMock();
        $client->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));

        $httpClient = new CachedHttpClient('1_meuptech', 'meuptech', array('base_url' => ''), $client);
        $httpClient->setCache($cache);
        $httpClient->fakeResponse = $response;

        $cache->expects($this->once())->method('get')->with('test');

        $httpClient->get('test');
    }

    /**
     * @test
     */
    public function shouldRenewCacheWhenResourceHasChanged()
    {
        $cache = $this->getCacheMock();
        $response = new Response(200);

        $client = $this->getBrowserMock();
        $client->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));

        $httpClient = new CachedHttpClient('1_meuptech', 'meuptech', array('base_url' => ''), $client);
        $httpClient->setCache($cache);

        $cache->expects($this->once())->method('set')->with('test', $response);
        $cache->expects($this->once())->method('getModifiedSince')->with('test')->will($this->returnValue(1256953732));

        $httpClient->get('test');
    }

    public function getCacheMock()
    {
        return $this->getMock('HomeApi\Client\HttpClient\Cache\CacheInterface');
    }
}
