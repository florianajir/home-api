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
 * Class ApiVersions
 */
class ApiVersions
{
    const LATEST = 'latest';
    const V1      = '1.0';

    public static function all()
    {
        return array(self::LATEST, self::V1);
    }
}
