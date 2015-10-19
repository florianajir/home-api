<?php
/**
 * This file is part of the PHP Client for 1001 Pharmacies API.
 *
 * (c) florianajir <https://github.com/florianajir/home-api>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HomeApi\Client;

/**
 * Class Api
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class Api
{
    const ORDERS = 'order';
    const PRODUCTS = 'product';
    const REASONS = 'reason';
    const BRANDS = 'brand';

    public static function all()
    {
        return array(
            self::ORDERS,
            self::PRODUCTS,
            self::REASONS,
            self::BRANDS
        );
    }
}
