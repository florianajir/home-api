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
 * MissingArgumentException.
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class MissingArgumentException extends ErrorException
{
    /**
     * @param string $required
     * @param int    $code
     * @param null   $previous
     */
    public function __construct($required, $code = 0, $previous = null)
    {
        if (is_string($required)) {
            $required = array($required);
        }

        parent::__construct(
            sprintf(
                'One or more of required ("%s") parameters is missing!',
                implode(
                    '", "',
                    $required
                )
            ),
            $code,
            $previous
        );
    }
}
