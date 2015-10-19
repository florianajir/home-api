<?php
/**
 * This file is part of the PHP Client for 1001 Pharmacies API.
 *
 * (c) florianajir <https://github.com/florianajir/home-api>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HomeApi\Client\Api;

/**
 * Product API
 *
 * @link   http://developers.florianajir.com/docs/v1/products.html
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class Product extends AbstractApi
{
    const PRODUCT_ID_TYPE_SKU = 'sku';
    const PRODUCT_ID_TYPE_EAN = 'ean';
    const PRODUCT_ID_TYPE_REF = 'reference';

    const BULK_UPLOAD_MERGE   = 'merge';
    const BULK_UPLOAD_REPLACE = 'replace';

    /**
     * Get Product by it's unique id
     *
     * @link http://developers.florianajir.com/docs/v1/products.html#tocAnchor-1-1-3
     *
     * @param string $sku product unique id
     *
     * @return array|null products
     */
    public function findBySku($sku)
    {
        return $this->find(self::PRODUCT_ID_TYPE_SKU, $sku);
    }

    /**
     * Get Product by it's ean
     *
     * @link http://developers.florianajir.com/docs/v1/products.html#tocAnchor-1-1-3
     *
     * @param string $ean product ean code
     *
     * @return array|null products
     */
    public function findByEan($ean)
    {
        return $this->find(self::PRODUCT_ID_TYPE_EAN, $ean);
    }

    /**
     * Get Product by it's reference
     *
     * @link http://developers.florianajir.com/docs/v1/products.html#tocAnchor-1-1-3
     *
     * @param string $reference product reference
     *
     * @return array|null products
     */
    public function findByReference($reference)
    {
        return $this->find(self::PRODUCT_ID_TYPE_REF, $reference);
    }

    /**
     * Find a product by it's given identifier
     *
     * @link http://developers.florianajir.com/docs/v1/products.html#tocAnchor-1-1-3
     *
     * @param string $identifierType   (see: self::PRODUCT_ID_TYPE_*)
     * @param string $identifier       Product id
     *
     * @return array|null products
     */
    public function find($identifierType, $identifier)
    {
        return $this
            ->get(
                self::BASE_API_PATH .
                'product/' .
                $identifierType .
                '/' .
                $identifier
            )
        ;
    }

    /**
     * Destock a quantity for given product
     *
     * @link http://developers.florianajir.com/docs/v1/products.html#tocAnchor-1-1-5
     *
     * @param string $identifierType   (see: self::PRODUCT_ID_TYPE_*)
     * @param string $identifier       Product id
     * @param int    $quantity         Quantity to destock
     *
     * @return array|null products
     */
    public function destock($identifierType, $identifier, $quantity)
    {
        return $this
            ->post(
                self::BASE_API_PATH .
                'product/' .
                $identifierType .
                '/' .
                $identifier .
                '/' .
                'destock',
                array(
                    'quantity' => $quantity
                ),
                array(
                    'Content-Type' => 'application/json'
                )
            )
        ;
    }

    /**
     *
     * @link http://developers.florianajir.com/docs/v1/products.html#tocAnchor-1-1-6
     *
     * @param string $identifierType   (see: self::PRODUCT_ID_TYPE_*)
     * @param string $identifier       Product id
     * @param int    $quantity         Product stock to set
     *
     * @return array|null products
     */
    public function updateQuantity($identifierType, $identifier, $quantity)
    {
        return $this
            ->patch(
                self::BASE_API_PATH .
                'product/' .
                $identifierType .
                '/' .
                $identifier .
                '/' .
                'quantity',
                array(
                    'quantity' => $quantity
                ),
                array(
                    'Content-Type' => 'application/json'
                )
            )
        ;
    }

    /**
     *
     * @link http://developers.florianajir.com/docs/v1/products.html#tocAnchor-1-1-7
     *
     * @param string $identifierType   (see: self::PRODUCT_ID_TYPE_*)
     * @param string $identifier       Product id
     * @param int    $warnQuantity     Warning quantity for stock alerts
     *
     * @return array|null products
     */
    public function updateWarningQuantity($identifierType, $identifier, $warnQuantity)
    {
        return $this
            ->patch(
                self::BASE_API_PATH .
                'product/' .
                $identifierType .
                '/' .
                $identifier .
                '/' .
                'warnquantity',
                array(
                    'warn_quantity' => $warnQuantity
                ),
                array(
                    'Content-Type' => 'application/json'
                )
            )
        ;
    }

    /**
     * Update a specific product
     *
     * @param string $identifierType    (see: self::PRODUCT_ID_TYPE_*)
     * @param string $identifier        Product id
     * @param int    $quantity          Product stock
     * @param int    $warnQuantity      Warning quantity for stock alerts
     * @param string $price             Product price
     * @param float  $active            Is product active on florianajir ?
     *
     * @return array|null               Product update result
     */
    public function update(
        $identifierType,
        $identifier,
        $quantity = null,
        $warnQuantity = null,
        $price = null,
        $active = null
    ) {
        $data = array();
        $data[$identifierType] = $identifier;
        if (!is_null($quantity)) {
            $data['quantity'] = $quantity;
        }
        if (!is_null($warnQuantity)) {
            $data['warn_quantity'] = $warnQuantity;
        }
        if (!is_null($price)) {
            $data['price'] = $price;
        }
        if (!is_null($active)) {
            $data['active'] = $active;
        }

        return $this
            ->put(
                self::BASE_API_PATH .
                'products/update',
                array($data),
                array(
                    'Content-Type' => 'application/json'
                )
            )
        ;
    }

    /**
     * Bulk update a list of products inventory (see documentation for data parameters)
     *
     * @description :
     *
     * ```JSON
     *  [
     *      {
     *      "ean": "xxx",
     *      "reference": "xxx",
     *      "sku": "xxx",
     *      "price": "xxx.xx",
     *      "active": true|false,
     *      "quantity": xxx,
     *      "warn_quantity": xxx
     *      },
     *      ...
     *  ]
     * ```
     *
     * You have to specify at least one product identifier (ean / reference / sku).
     * /!\ Ean or References may correspond to more than one product, so update will be on each product.
     *
     * @param array  $products      List of products inventory
     * @param string $updateType    Fusion type for bulk update (see: self::BULK_UPLOAD_*)
     *
     * @return array|null       Product update result
     */
    public function bulkUpdate(array $products, $updateType = self::BULK_UPLOAD_MERGE)
    {
        return $this
            ->put(
                self::BASE_API_PATH .
                'products/update' . ($updateType == self::BULK_UPLOAD_REPLACE ? '?type=' . self::BULK_UPLOAD_REPLACE : null) ,
                $products,
                array(
                    'Content-Type' => 'application/json'
                )
            )
        ;
    }
}
