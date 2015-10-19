<?php
/**
 * This file is part of the PHP Client for 1001 Pharmacies API.
 *
 * (c) florianajir <https://github.com/florianajir/home-api>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HomeApi\Client\Tests\Api;

use HomeApi\Client\Api\AbstractApi;
use Guzzle\Http\Message\Response;
use HomeApi\Client\MeupApiClient;

/**
 * Class AbstractApiTest
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class AbstractApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldPassGETRequestToClient()
    {
        $expectedArray = array('value');

        $httpClient = $this->getHttpMock();
        $httpClient->expects($this->any())
            ->method('get')
            ->with(
                '/path',
                array('param1' => 'param1value'),
                array('header1' => 'header1value')
            )
            ->will($this->returnValue($expectedArray))
        ;
        $client = $this->getClientMock();
        $client->setHttpClient($httpClient);

        $api = $this->getAbstractApiObject($client);

        $this->assertEquals(
            $expectedArray,
            $api->get(
                '/path',
                array('param1' => 'param1value'),
                array('header1' => 'header1value')
            )
        )
        ;
    }

    /**
     * @test
     */
    public function shouldPassPOSTRequestToClient()
    {
        $expectedArray = array('value');

        $httpClient = $this->getHttpMock();
        $httpClient->expects($this->once())
            ->method('post')
            ->with(
                '/path',
                array('param1' => 'param1value'),
                array('option1' => 'option1value')
            )
            ->will($this->returnValue($expectedArray))
        ;
        $client = $this->getClientMock();
        $client->setHttpClient($httpClient);

        $api = $this->getAbstractApiObject($client);

        $this->assertEquals(
            $expectedArray,
            $api->post(
                '/path',
                array('param1' => 'param1value'),
                array('option1' => 'option1value')
            )
        )
        ;
    }

    /**
     * @test
     */
    public function shouldPassPATCHRequestToClient()
    {
        $expectedArray = array('value');

        $httpClient = $this->getHttpMock();
        $httpClient->expects($this->once())
            ->method('patch')
            ->with(
                '/path',
                array('param1' => 'param1value'),
                array('option1' => 'option1value')
            )
            ->will($this->returnValue($expectedArray))
        ;
        $client = $this->getClientMock();
        $client->setHttpClient($httpClient);

        $api = $this->getAbstractApiObject($client);

        $this->assertEquals(
            $expectedArray,
            $api->patch(
                '/path',
                array('param1' => 'param1value'),
                array('option1' => 'option1value')
            )
        )
        ;
    }

    /**
     * @test
     */
    public function shouldPassPUTRequestToClient()
    {
        $expectedArray = array('value');

        $httpClient = $this->getHttpMock();
        $httpClient->expects($this->once())
            ->method('put')
            ->with(
                '/path',
                array('param1' => 'param1value'),
                array('option1' => 'option1value')
            )
            ->will($this->returnValue($expectedArray))
        ;
        $client = $this->getClientMock();
        $client->setHttpClient($httpClient);

        $api = $this->getAbstractApiObject($client);

        $this->assertEquals(
            $expectedArray,
            $api->put(
                '/path',
                array('param1' => 'param1value'),
                array('option1' => 'option1value')
            )
        )
        ;
    }

    /**
     * @test
     */
    public function shouldPassDELETERequestToClient()
    {
        $expectedArray = array('value');

        $httpClient = $this->getHttpMock();
        $httpClient->expects($this->once())
            ->method('delete')
            ->with(
                '/path',
                array('param1' => 'param1value'),
                array('option1' => 'option1value')
            )
            ->will($this->returnValue($expectedArray))
        ;
        $client = $this->getClientMock();
        $client->setHttpClient($httpClient);

        $api = $this->getAbstractApiObject($client);

        $this->assertEquals(
            $expectedArray,
            $api->delete(
                '/path',
                array('param1' => 'param1value'),
                array('option1' => 'option1value')
            )
        )
        ;
    }

    /**
     * @test
     */
    public function shouldNotPassEmptyRefToClient()
    {
        $expectedResponse = new Response('value');

        $httpClient = $this->getHttpMock();
        $httpClient->expects($this->any())
            ->method('get')
            ->with(
                '/path',
                array()
            )
            ->will($this->returnValue($expectedResponse))
        ;
        $client = $this->getClientMock();
        $client->setHttpClient($httpClient);

        $api = new ExposedAbstractApiTestInstance($client);
        $api->get(
            '/path',
            array('ref' => null)
        )
        ;
    }

    protected function getAbstractApiObject($client)
    {
        return new AbstractApiTestInstance($client);
    }

    /**
     * @return \HomeApi\Client\MeupApiClient
     */
    protected function getClientMock()
    {
        return new MeupApiClient("clientId", "clientSecret", 'latest', $this->getHttpMock());
    }

    /**
     * @return \HomeApi\Client\HttpClient\HttpClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getHttpMock()
    {
        return $this->getMock(
            'HomeApi\Client\HttpClient\HttpClient',
            array(),
            array(
                array(),
                $this->getHttpClientMock()
            )
        );
    }

    protected function getHttpClientMock()
    {
        $mock = $this->getMock(
            'Guzzle\Http\Client',
            array('send')
        )
        ;
        $mock->expects($this->any())
            ->method('send')
        ;

        return $mock;
    }
}

/**
 * Class AbstractApiTestInstance
 *
 * @todo refactor this ****
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class AbstractApiTestInstance extends AbstractApi
{
    /**
     * {@inheritDoc}
     */
    public function get($path, array $parameters = array(), $requestHeaders = array())
    {
        return $this->client->getHttpClient()
            ->get(
                $path,
                $parameters,
                $requestHeaders
            )
            ;
    }

    /**
     * {@inheritDoc}
     */
    public function post($path, array $parameters = array(), $requestHeaders = array())
    {
        return $this->client->getHttpClient()
            ->post(
                $path,
                $parameters,
                $requestHeaders
            )
            ;
    }

    /**
     * {@inheritDoc}
     */
    public function postRaw($path, $body, $requestHeaders = array())
    {
        return $this->client->getHttpClient()
            ->post(
                $path,
                $body,
                $requestHeaders
            )
            ;
    }

    /**
     * {@inheritDoc}
     */
    public function patch($path, array $parameters = array(), $requestHeaders = array())
    {
        return $this->client->getHttpClient()
            ->patch(
                $path,
                $parameters,
                $requestHeaders
            )
            ;
    }

    /**
     * {@inheritDoc}
     */
    public function put($path, array $parameters = array(), $requestHeaders = array())
    {
        return $this->client->getHttpClient()
            ->put(
                $path,
                $parameters,
                $requestHeaders
            )
            ;
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path, array $parameters = array(), $requestHeaders = array())
    {
        return $this->client->getHttpClient()
            ->delete(
                $path,
                $parameters,
                $requestHeaders
            )
            ;
    }
}

/**
 * Class ExposedAbstractApiTestInstance
 *
 * @todo refactor this ****
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class ExposedAbstractApiTestInstance extends AbstractApi
{
    /**
     * {@inheritDoc}
     */
    public function get($path, array $parameters = array(), $requestHeaders = array())
    {
        return parent::get(
            $path,
            $parameters,
            $requestHeaders
        )
            ;
    }
}
