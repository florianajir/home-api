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
use HomeApi\Client\Api\Order;

/**
 * Class OrderTest
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class OrderTest extends TestCase
{
    /**
     * @test
     */
    public function findOrderTest()
    {
        $expectedArray = array('complete_invoice_number' => '1234567890');

        /** @var Order|\PHPUnit_Framework_MockObject_MockObject $api */
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('api/orders/1234567890')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->find('1234567890'));
    }

    /**
     * @test
     */
    public function generateParcelLabelTest()
    {
        $baseUri = 'http://www.florianajir.com';

        $expectedArray = array(
            'uri' => $baseUri . '/etiquette/1234567890?token=115ff9df6a7904df8c5493649eeae4323fb0ead0&store=26'
        );

        /** @var Order|\PHPUnit_Framework_MockObject_MockObject $api */
        $api = $this->getApiMock();
        $api->expects($this->once())
            ->method('get')
            ->with('api/orders/1234567890/parcellabel')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->parcellabel('1234567890'));
    }

    protected function getApiClass()
    {
        return 'HomeApi\Client\Api\Order';
    }
}
