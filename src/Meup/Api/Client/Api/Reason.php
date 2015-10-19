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
 * Reason API
 *
 * @link   http://developers.florianajir.com/docs/v1/tickets.html
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class Reason extends AbstractApi
{
    /**
     * Get Sav Reason list
     *
     * @link http://developers.florianajir.com/docs/v1/tickets.html#tocAnchor-1-3-1
     *
     * @return null|array sav reasons
     */
    public function all()
    {
        return $this->get('api/sav/reasons');
    }
}
