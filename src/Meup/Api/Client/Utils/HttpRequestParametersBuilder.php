<?php
/**
 * This file is part of the Sherlock Project
 *
 * (c) florianajir <http://github.com/florianajir/sherlock>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HomeApi\Client\Utils;

class HttpRequestParametersBuilder
{
    public static function buildFromArray($parameters = array())
    {
        $queryString = "";
        if ($parameters && is_array($parameters)) {
            $queryString .= "?";
            foreach ($parameters as $param => $value) {
                $queryString .= $param . "=" . $value . "&";
            }
            $queryString = rtrim($queryString, "&");
        }
        return $queryString;
    }
}
