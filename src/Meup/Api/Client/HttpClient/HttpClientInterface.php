<?php
/**
 * This file is part of the PHP Client for 1001 Pharmacies API.
 *
 * (c) florianajir <https://github.com/florianajir/home-api>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HomeApi\Client\HttpClient;

use HomeApi\Client\Exception\ApiNotRespondingException;
use HomeApi\Client\Exception\AuthenticationException;
use HomeApi\Client\Exception\ErrorException;
use HomeApi\Client\Exception\InvalidArgumentException;
use Guzzle\Http\Message\Response;
use HomeApi\Client\Exception\RuntimeException;

/**
 * Performs requests on florianajir API. API documentation should be self-explanatory.
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
interface HttpClientInterface
{
    /**
     * Send a GET request.
     *
     * @param string $path       Request path
     * @param array  $parameters GET Parameters
     * @param array  $headers    Reconfigure the request headers for this call only
     *
     * @return Response
     */
    public function get($path, array $parameters = array(), array $headers = array());

    /**
     * Send a POST request.
     *
     * @param string $path    Request path
     * @param mixed  $body    Request body
     * @param array  $headers Reconfigure the request headers for this call only
     *
     * @return Response
     */
    public function post($path, $body = null, array $headers = array());

    /**
     * Send a PATCH request.
     *
     * @param string $path    Request path
     * @param mixed  $body    Request body
     * @param array  $headers Reconfigure the request headers for this call only
     *
     * @internal param array $parameters Request body
     *
     * @return Response
     */
    public function patch($path, $body = null, array $headers = array());

    /**
     * Send a PUT request.
     *
     * @param string $path    Request path
     * @param mixed  $body    Request body
     * @param array  $headers Reconfigure the request headers for this call only
     *
     * @return Response
     */
    public function put($path, $body, array $headers = array());

    /**
     * Send a DELETE request.
     *
     * @param string $path    Request path
     * @param mixed  $body    Request body
     * @param array  $headers Reconfigure the request headers for this call only
     *
     * @return Response
     */
    public function delete($path, $body = null, array $headers = array());

    /**
     * Send a request to the server, receive a response,
     * decode the response and returns an associative array.
     *
     * @param string $path       Request path
     * @param mixed  $body       Request body
     * @param string $httpMethod HTTP method to use
     * @param array  $headers    Request headers
     *
     * @throws ApiNotRespondingException
     * @throws AuthenticationException
     * @throws RuntimeException
     * @throws ErrorException
     *
     * @return Response
     */
    public function request($path, $body, $httpMethod = 'GET', array $headers = array());

    /**
     * Change an option value.
     *
     * @param string $name  The option name
     * @param mixed  $value The value
     *
     * @throws InvalidArgumentException
     */
    public function setOption($name, $value);

    /**
     * Set HTTP headers.
     *
     * @param array $headers
     */
    public function setHeaders(array $headers);

    /**
     * Authenticate a store for all next requests.
     *
     * @param string    $publicKey    florianajir Store Client Id
     * @param string    $secretKey    florianajir Store Secret Key
     *
     * @throws InvalidArgumentException If no authentication method was given
     * @throws AuthenticationException  If Store not authenticated after login
     */
    public function authenticate($publicKey, $secretKey);
}
