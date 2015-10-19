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
 * Api interface.
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
interface ApiInterface
{
    public function getPerPage();

    public function setPerPage($perPage);
}
