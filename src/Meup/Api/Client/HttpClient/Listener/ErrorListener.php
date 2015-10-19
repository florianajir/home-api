<?php
/**
 * This file is part of the PHP Client for 1001 Pharmacies API.
 *
 * (c) florianajir <https://github.com/florianajir/home-api>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HomeApi\Client\HttpClient\Listener;

use HomeApi\Client\HttpClient\Message\ResponseParser;
use HomeApi\Client\Exception\ApiLimitExceedException;
use HomeApi\Client\Exception\ErrorException;
use HomeApi\Client\Exception\RuntimeException;
use HomeApi\Client\Exception\ValidationFailedException;
use Guzzle\Common\Event;

/**
 * Class ErrorListener
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class ErrorListener
{
    /**
     * @var array
     */
    private $options;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function onRequestError(Event $event)
    {
        /** @var $request \Guzzle\Http\Message\Request */
        $request = $event['request'];
        $response = $request->getResponse();

        if ($response->isClientError() || $response->isServerError()) {
            // Rate Limit
            $remaining = (string) $response->getHeader('X-RateLimit-Remaining');
            if (null != $remaining && 1 > $remaining && 'rate_limit' !== substr($request->getResource(), 1, 10)) {
                throw new ApiLimitExceedException($this->options['api_limit']);
            }

            $content = ResponseParser::getContent($response);
            if (is_array($content) && isset($content['message'])) {
                if (400 == $response->getStatusCode()) {
                    throw new ErrorException($content['message'], 400);
                } elseif (500 == $response->getStatusCode()) {
                    throw new ErrorException($content['message'], 500);
                } elseif (401 == $response->getStatusCode()) {
                    throw new ErrorException($content['errors']['error_description'], 401);
                } elseif (422 == $response->getStatusCode() && isset($content['errors'])) {
                    $errors = array();
                    foreach ($content['errors'] as $error) {
                        switch ($error['code']) {
                            case 'missing':
                                $errors[] =
                                    sprintf(
                                        'The %s %s does not exist, for resource "%s"',
                                        $error['field'],
                                        $error['value'],
                                        $error['resource']
                                    )
                                ;
                                break;

                            case 'missing_field':
                                $errors[] =
                                    sprintf(
                                        'Field "%s" is missing, for resource "%s"',
                                        $error['field'],
                                        $error['resource']
                                    )
                                ;
                                break;

                            case 'invalid':
                                $errors[] =
                                    sprintf(
                                        'Field "%s" is invalid, for resource "%s"',
                                        $error['field'],
                                        $error['resource']
                                    )
                                ;
                                break;

                            case 'already_exists':
                                $errors[] =
                                    sprintf(
                                        'Field "%s" already exists, for resource "%s"',
                                        $error['field'],
                                        $error['resource']
                                    )
                                ;
                                break;

                            default:
                                $errors[] = $error['message'];
                                break;
                        }
                    }

                    throw new ValidationFailedException('Validation Failed: ' . implode(', ', $errors), 422);
                }
            }

            $msg = "";
            $code = $response->getStatusCode();
            if (is_array($content) && array_key_exists('error', $content)) {
                if (is_array($content['error']) && array_key_exists('error_description', $content)) {
                    $msg = $content['error_description'];
                } else {
                    // Oauth Error Format
                    if (is_array($content['error'])) {
                        $msg = $content['error']['message'];
                        $code = $content['error']['code'];
                    } else {
                        $msg = $content['error'];
                    }
                }
            } elseif (is_array($content) && array_key_exists('message', $content)) {
                $msg = $content['message'];
            } else {
                $msg = $content;
            }

            throw new RuntimeException($msg, $code);
        };
    }
}
