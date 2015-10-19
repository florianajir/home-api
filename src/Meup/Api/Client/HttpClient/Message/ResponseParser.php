<?php
/**
 * This file is part of the PHP Client for 1001 Pharmacies API.
 *
 * (c) florianajir <https://github.com/florianajir/home-api>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HomeApi\Client\HttpClient\Message;

use Guzzle\Http\Message\Response;
use HomeApi\Client\Exception\ApiLimitExceedException;

/**
 * Class ResponseParser
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class ResponseParser
{
    public static function getContent(Response $response)
    {
        $body    = $response->getBody(true);
        $content = json_decode($body, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            return $body;
        }

        return $content;
    }

    public static function getPerPage(Response $response)
    {
        $body    = $response->getBody(true);
        $content = json_decode($body, true);

        if (empty($content) && !array_key_exists('limit', $content)) {
            return null;
        }

        return $content['limit'];
    }

    public static function getCurrentPage(Response $response)
    {
        $body    = $response->getBody(true);
        $content = json_decode($body, true);

        if (empty($content) && !array_key_exists('page', $content)) {
            return null;
        }

        return $content['page'];
    }

    public static function getMaxPages(Response $response)
    {
        $body    = $response->getBody(true);
        $content = json_decode($body, true);

        if (empty($content) && !array_key_exists('pages', $content)) {
            return null;
        }

        return $content['pages'];
    }

    public static function getPagination(Response $response)
    {
        $body    = $response->getBody(true);
        $content = json_decode($body, true);

        if (empty($content)) {
            return null;
        }

        $pagination = array();
        if (!array_key_exists('_links', $content)) {
            return null;
        }

        foreach ($content['_links'] as $linkType => $link) {
            if (in_array($linkType, array('self', 'first', 'last', 'next', 'previous'))) {
                $pagination[$linkType] = $link['href'];
            }
        }

        return $pagination;
    }

    public static function getApiLimit(Response $response)
    {
        $remainingCalls = $response->getHeader('X-RateLimit-Remaining');

        if (null !== $remainingCalls && 1 > $remainingCalls) {
            throw new ApiLimitExceedException($remainingCalls);
        }

        return $remainingCalls;
    }
}
