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

use HomeApi\Client\HttpClient\Message\ResponseParser;
use HomeApi\Client\MeupApiClient;

/**
 * Abstract class for Api classes.
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
abstract class AbstractApi implements ApiInterface
{
    const BASE_API_PATH = "api/";

    /**
     * The client.
     *
     * @var MeupApiClient
     */
    protected $client;

    /**
     * Number of items per page (Hateoas pagination).
     *
     * @var null|int
     */
    protected $perPage;

    /**
     * @param MeupApiClient $client
     */
    public function __construct(MeupApiClient $client)
    {
        $this->client = $client;
    }

    public function configure()
    {
    }

    /**
     * @return null|int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * @param null|int $perPage
     *
     * @return self
     */
    public function setPerPage($perPage)
    {
        $this->perPage = (null === $perPage ? $perPage : (int) $perPage);

        return $this;
    }

    /**
     * Send a GET request with query parameters.
     *
     * @param string $path           Request path.
     * @param array  $parameters     GET parameters.
     * @param array  $requestHeaders Request Headers.
     *
     * @return \Guzzle\Http\EntityBodyInterface|mixed|string
     */
    protected function get($path, array $parameters = array(), $requestHeaders = array())
    {
        if (null !== $this->perPage && !isset($parameters['perPage'])) {
            $parameters['perPage'] = $this->perPage;
        }
        if (array_key_exists('ref', $parameters) && is_null($parameters['ref'])) {
            unset($parameters['ref']);
        }
        $response = $this->client->getHttpClient()->get($path, $parameters, $requestHeaders);

        return ResponseParser::getContent($response);
    }

    /**
     * Send a POST request with JSON-encoded parameters.
     *
     * @param string $path           Request path.
     * @param array  $parameters     POST parameters to be JSON encoded.
     * @param array  $requestHeaders Request headers.
     *
     * @return \Guzzle\Http\EntityBodyInterface|mixed|string
     */
    protected function post($path, array $parameters = array(), $requestHeaders = array())
    {
        return $this->postRaw(
            $path,
            $this->createJsonBody($parameters),
            $requestHeaders
        );
    }

    /**
     * Send a POST request with raw data.
     *
     * @param string $path           Request path.
     * @param string $body           Request body.
     * @param array  $requestHeaders Request headers.
     *
     * @return \Guzzle\Http\EntityBodyInterface|mixed|string
     */
    protected function postRaw($path, $body, $requestHeaders = array())
    {
        $response = $this->client->getHttpClient()->post(
            $path,
            $body,
            $requestHeaders
        );

        return ResponseParser::getContent($response);
    }

    /**
     * Send a PATCH request with JSON-encoded parameters.
     *
     * @param string $path           Request path.
     * @param array  $parameters     POST parameters to be JSON encoded.
     * @param array  $requestHeaders Request headers.
     *
     * @return \Guzzle\Http\EntityBodyInterface|mixed|string
     */
    protected function patch($path, array $parameters = array(), $requestHeaders = array())
    {
        $response = $this->client->getHttpClient()->patch(
            $path,
            $this->createJsonBody($parameters),
            $requestHeaders
        );

        return ResponseParser::getContent($response);
    }

    /**
     * Send a PUT request with JSON-encoded parameters.
     *
     * @param string $path           Request path.
     * @param array  $parameters     POST parameters to be JSON encoded.
     * @param array  $requestHeaders Request headers.
     *
     * @return \Guzzle\Http\EntityBodyInterface|mixed|string
     */
    protected function put($path, array $parameters = array(), $requestHeaders = array())
    {
        $response = $this->client->getHttpClient()->put(
            $path,
            $this->createJsonBody($parameters),
            $requestHeaders
        );

        return ResponseParser::getContent($response);
    }

    /**
     * Send a DELETE request with JSON-encoded parameters.
     *
     * @param string $path           Request path.
     * @param array  $parameters     POST parameters to be JSON encoded.
     * @param array  $requestHeaders Request headers.
     *
     * @return \Guzzle\Http\EntityBodyInterface|mixed|string
     */
    protected function delete($path, array $parameters = array(), $requestHeaders = array())
    {
        $response = $this->client->getHttpClient()->delete(
            $path,
            $this->createJsonBody($parameters),
            $requestHeaders
        );

        return ResponseParser::getContent($response);
    }

    /**
     * Create a JSON encoded version of an array of parameters.
     *
     * @param array $parameters Request parameters
     *
     * @return null|string
     */
    protected function createJsonBody(array $parameters)
    {
        return (count($parameters) === 0) ? null : json_encode($parameters, empty($parameters) ? JSON_FORCE_OBJECT : 0);
    }
}
