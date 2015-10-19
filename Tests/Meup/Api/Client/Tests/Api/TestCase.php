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

use HomeApi\Client\MeupApiClient;

/**
 * Class TestCase
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    abstract protected function getApiClass();

    protected function getApiMock()
    {
        $httpClient = $this->getMock(
            'Guzzle\Http\Client',
            array('send')
        )
        ;
        $httpClient->expects($this->any())
            ->method('send')
        ;
        $mock = $this->getMock(
            'HomeApi\Client\HttpClient\HttpClient',
            array(),
            array(array(), $httpClient)
        )
        ;
        $client = new MeupApiClient("clientId", "clientSecret", "latest", $mock);

        return $this->getMockBuilder($this->getApiClass())
            ->setMethods(array('get', 'post', 'postRaw', 'patch', 'delete', 'put'))
            ->setConstructorArgs(array($client))
            ->getMock()
            ;
    }
}
