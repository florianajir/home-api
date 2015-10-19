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

use Guzzle\Http\Client;
use Guzzle\Http\Message\Request;
use HomeApi\Client\HttpClient\Listener\AuthListener;

/**
 * Class AuthListenerTest
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class AuthListenerTest extends \PHPUnit_Framework_TestCase
{
    private $baseUri = 'https://local.api.florianajir.com';

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function shouldHaveKnownMethodName()
    {
        $request = new Request('GET', '/oauth/v2/token');
        $listener = new AuthListener(new Client(), $this->baseUri, 'clientId', 'clientSecret', 'unknown');
        $listener->onRequestBeforeSend($this->getEventMock($request));
    }

    /**
     * @test
     */
    public function shouldDoNothingForHaveNullMethod()
    {
        $request = $this->getMock('Guzzle\Http\Message\RequestInterface');
        $request->expects($this->never())
            ->method('addHeader');
        $request->expects($this->never())
            ->method('fromUrl');
        $request->expects($this->never())
            ->method('getUrl');

        $listener = new AuthListener(new Client(), $this->baseUri, 'clientId', 'clientSecret', null);
        $listener->onRequestBeforeSend($this->getEventMock($request));
    }

    /**
     * @test
     */
    public function shouldDoNothingForPostSend()
    {
        $request = $this->getMock('Guzzle\Http\Message\RequestInterface');
        $request->expects($this->never())
            ->method('addHeader');
        $request->expects($this->never())
            ->method('fromUrl');
        $request->expects($this->never())
            ->method('getUrl');

        $listener = new AuthListener(new Client(), $this->baseUri, 'clientId', 'clientSecret');
        $listener->onRequestBeforeSend($this->getEventMock($request));
    }

    /**
     * @param null $request
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Guzzle\Common\Event
     */
    private function getEventMock($request = null)
    {
        $mock = $this->getMockBuilder('Guzzle\Common\Event')->getMock();

        if ($request) {
            $mock
                ->expects($this->any())
                ->method('offsetGet')
                ->will($this->returnValue($request))
            ;
        }

        return $mock;
    }
}
