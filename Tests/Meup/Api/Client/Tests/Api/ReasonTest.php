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

/**
 * Class ReasonTest
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class ReasonTest extends TestCase
{
    /**
     * @test
     */
    public function getAllReasonsTest()
    {
        $expectedArray = array('page' => '1');

        $api = $this->getApiMock();
        $api
            ->expects($this->once())
            ->method('get')
            ->with('api/sav/reasons')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->all());
    }

    protected function getApiClass()
    {
        return 'HomeApi\Client\Api\Reason';
    }
}
