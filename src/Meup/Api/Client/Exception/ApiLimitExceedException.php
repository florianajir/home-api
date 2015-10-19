<?php
/**
 * This file is part of the PHP Client for 1001 Pharmacies API.
 *
 * (c) florianajir <https://github.com/florianajir/home-api>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HomeApi\Client\Exception;

/**
 * ApiLimitExceedException.
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class ApiLimitExceedException extends RuntimeException
{
    /**
     * @param int  $limit
     * @param int  $code
     * @param null $previous
     */
    public function __construct($limit = 5000, $code = 0, $previous = null)
    {
        parent::__construct('You have reached HomeApi hour limit! Actual limit is: '. $limit, $code, $previous);
    }
}
