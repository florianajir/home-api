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
 * Brand API
 *
 * @link   http://developers.florianajir.com/docs/v1/brands.html
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class Brand extends AbstractApi
{
    /**
     * Get Brands list
     *
     * @link http://developers.florianajir.com/docs/v1/brands.html#tocAnchor-1-3-1
     *
     * @return null|array sav reasons
     */
    public function all()
    {
        return $this->get('api/brands');
    }

    /**
     * Get Brand Products list
     *
     * @link http://developers.florianajir.com/docs/v1/brands.html#tocAnchor-1-3-2
     *
     * @param string $brandId
     *
     * @return array|null sav reasons
     */
    public function getProducts($brandId)
    {
        return $this->get(sprintf('api/brands/%s/products/', $brandId));
    }
}
