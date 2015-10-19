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

use HomeApi\Client\Utils\HttpRequestParametersBuilder;

/**
 * Order API
 *
 * @link   http://developer.florianajir.com/docs/v1/orders.html
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class Order extends AbstractApi
{
    /**
     * Get Order by it's identifier (complete_invoice_number).
     *
     * @link http://developers.florianajir.com/docs/v1/orders.html#tocAnchor-1-3-1
     *
     * @param string $identifier the identifier to search
     *
     * @return null|array order found
     */
    public function find($identifier)
    {
        return $this->get('api/orders/'.rawurlencode($identifier));
    }

    /**
     * Get a list of orders awaiting preparation without details.
     *
     * @link http://developers.florianajir.com/docs/v1/orders.html#tocAnchor-1-3-1
     *
     * @param array $parameters
     *
     * @return array|null orders found
     */
    public function all($parameters = array())
    {
        return $this->get('api/orders' . HttpRequestParametersBuilder::buildFromArray($parameters));
    }

    /**
     * Generate parcel label for given order.
     *
     * @link http://developers.florianajir.com/docs/v1/orders.html#tocAnchor-1-3-1
     *
     * @param string $identifier the order identifier
     *
     * @return array|null orders found
     */
    public function parcellabel($identifier)
    {
        return $this->get("api/orders/{$identifier}/parcellabel");
    }

    /**
     * Expediate given order
     *
     * @link http://developers.florianajir.com/docs/v1/orders.html#tocAnchor-1-3-1
     *
     * @param string $identifier the order identifier
     *
     * @return array|null orders found
     */
    public function expediate($identifier)
    {
        return $this->post(
            "api/orders/{$identifier}/expediate",
            array(),
            array(
                'Content-Type' => 'application/json'
            )
        );
    }
}
