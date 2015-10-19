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
use HomeApi\Client\Api\Product;

/**
 * Class ProductTest
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class ProductTest extends TestCase
{
    /**
     * @test
     */
    public function findBySkuTest()
    {
        $expectedArray = array('sku' => '2059');

        $api = $this->getApiMock();
        $api
            ->expects($this->once())
            ->method('get')
            ->with('api/product/sku/2059')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->findBySku('2059'));
    }

    /**
     * @test
     */
    public function findByEanTest()
    {
        $expectedArray = array('ean' => '3700281702385');

        $api = $this->getApiMock();
        $api
            ->expects($this->once())
            ->method('get')
            ->with('api/product/ean/3700281702385')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->findByEan('3700281702385'));
    }

    /**
     * @test
     */
    public function findByReferenceTest()
    {
        $expectedArray = array('reference' => '5117623');

        $api = $this->getApiMock();
        $api
            ->expects($this->once())
            ->method('get')
            ->with('api/product/reference/5117623')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->findByReference('5117623'));
    }

    /**
     * @test
     */
    public function findTest()
    {
        $expectedArray = array('reference' => '5117623');

        $api = $this->getApiMock();
        $api
            ->expects($this->once())
            ->method('get')
            ->with('api/product/reference/5117623')
            ->will($this->returnValue($expectedArray));

        $this->assertEquals($expectedArray, $api->find(Product::PRODUCT_ID_TYPE_REF, '5117623'));
    }

    /**
     * @test
     */
    public function destockTest()
    {
        $expectedArray = json_decode('
            [
              {
                "cip13": "1234567890",
                "ean": "1234567890",
                "id": 1234567890,
                "cip7": "1234567890",
                "product_name": "xxxxxxxxx",
                "brand_name": "xxxx",
                "brand_sku": 38,
                "suggested_price": 11.54,
                "currency": "EUR",
                "price": 11.44,
                "active": false,
                "quantity": 98,
                "warn_quantity": 99
              }
            ]
        ');

        $api = $this->getApiMock();
        $api
            ->expects($this->once())
            ->method('post')
            ->with('api/product/sku/1234567890/destock')
            ->will($this->returnValue($expectedArray));

        $result = $api->destock(Product::PRODUCT_ID_TYPE_SKU, '1234567890', 5);
        $this->assertEquals($expectedArray, $result);
    }

    /**
     * @test
     */
    public function updateQuantityTest()
    {
        $expectedArray = json_decode('
            [
              {
                "cip13": "1234567890",
                "ean": "1234567890",
                "id": 1234567890,
                "cip7": "1234567890",
                "product_name": "xxxxxxxxx",
                "brand_name": "xxxx",
                "brand_sku": 38,
                "suggested_price": 11.54,
                "currency": "EUR",
                "price": 11.44,
                "active": false,
                "quantity": 98,
                "warn_quantity": 99
              }
            ]
        ');

        $api = $this->getApiMock();
        $api
            ->expects($this->once())
            ->method('patch')
            ->with('api/product/sku/1234567890/quantity')
            ->will($this->returnValue($expectedArray));

        $result = $api->updateQuantity(Product::PRODUCT_ID_TYPE_SKU, '1234567890', 5);
        $this->assertEquals($expectedArray, $result);
    }

    /**
     * @test
     */
    public function updateWarnQuantityTest()
    {
        $expectedArray = json_decode('
            [
              {
                "cip13": "1234567890",
                "ean": "1234567890",
                "id": 1234567890,
                "cip7": "1234567890",
                "product_name": "xxxxxxxxx",
                "brand_name": "xxxx",
                "brand_sku": 38,
                "suggested_price": 11.54,
                "currency": "EUR",
                "price": 11.44,
                "active": false,
                "quantity": 98,
                "warn_quantity": 99
              }
            ]
        ');

        $api = $this->getApiMock();
        $api
            ->expects($this->once())
            ->method('patch')
            ->with('api/product/sku/1234567890/warnquantity')
            ->will($this->returnValue($expectedArray));

        $result = $api->updateWarningQuantity(Product::PRODUCT_ID_TYPE_SKU, '1234567890', 5);
        $this->assertEquals($expectedArray, $result);
    }

    /**
     * @test
     */
    public function updateProductTest()
    {
        $expectedArray = json_decode('
            {
              "errors": 0,
              "success": 1,
              "parsed": 1,
              "founds": 1
            }
        ');

        $api = $this->getApiMock();
        $api
            ->expects($this->once())
            ->method('put')
            ->with('api/products/update')
            ->will($this->returnValue($expectedArray));

        $result = $api->update(
            Product::PRODUCT_ID_TYPE_SKU,
            '1234567890',
            5,
            4,
            10.20,
            true
        );
        $this->assertEquals($expectedArray, $result);
    }

    /**
     * @test
     */
    public function bulkUpdateProductTest()
    {
        $data = json_decode('
            [
              {
                "cip13": "1234567890",
                "ean": "1234567890",
                "id": 1234567890,
                "cip7": "1234567890",
                "product_name": "xxxxxxxxx",
                "brand_name": "xxxx",
                "brand_sku": 38,
                "suggested_price": 11.54,
                "currency": "EUR",
                "price": 11.44,
                "active": false,
                "quantity": 98,
                "warn_quantity": 99
              },
              {
                "cip13": "1234567890",
                "ean": "1234567890",
                "id": 1234567890,
                "cip7": "1234567890",
                "product_name": "xxxxxxxxx",
                "brand_name": "xxxx",
                "brand_sku": 38,
                "suggested_price": 11.54,
                "currency": "EUR",
                "price": 11.44,
                "active": false,
                "quantity": 98,
                "warn_quantity": 99
              }
            ]
        ');

        $expectedArray = json_decode('
            {
              "errors": 0,
              "success": 2,
              "parsed": 2,
              "founds": 2
            }
        ');

        $api = $this->getApiMock();
        $api
            ->expects($this->once())
            ->method('put')
            ->with('api/products/update')
            ->will($this->returnValue($expectedArray));

        $result = $api->bulkUpdate($data, Product::BULK_UPLOAD_MERGE);
        $this->assertEquals($expectedArray, $result);
    }

    protected function getApiClass()
    {
        return 'HomeApi\Client\Api\Product';
    }
}
