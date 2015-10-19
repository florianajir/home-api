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

use HomeApi\Client\Api\ApiInterface;
use HomeApi\Client\Exception\BadMethodCallException;
use HomeApi\Client\Exception\InvalidArgumentException;
use HomeApi\Client\HttpClient\HttpClient;
use HomeApi\Client\HttpClient\HttpClientInterface;
use Symfony\Component\Yaml\Parser;

/**
 * Simple PHP Client for florianajir Restfull API.
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class MeupApiClient
{
    /**
     * @var string
     */
    private $client_id;

    /**
     * @var string
     */
    private $client_secret;

    /**
     * @var string
     */
    private $version;

    /**
     * The HttpClient used to communicate with florianajir Api.
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Instantiate a new florianajir Api HttpClient.
     *
     * @param string                   $clientId        Client identifier
     * @param string                   $clientSecret    Client secret key
     * @param string                   $version         Api version to use
     * @param null|HttpClientInterface $httpClient      florianajir http client
     *
     * @throws InvalidArgumentException If no credentials or wrong version were given
     */
    public function __construct(
        $clientId,
        $clientSecret,
        $version = ApiVersions::LATEST,
        HttpClientInterface $httpClient = null
    ) {

        if (null === $clientId || null === $clientSecret) {
            throw new InvalidArgumentException('You need to specify yours credentials!');
        }

        if (!in_array($version, ApiVersions::all())) {
            throw new InvalidArgumentException('You need to specify a valid Api version!');
        }

        $this->client_id        = $clientId;
        $this->client_secret    = $clientSecret;
        $this->version          = $version;
        $this->httpClient       = $httpClient;
    }

    /**
     * @param string $name
     *
     * @throws InvalidArgumentException
     *
     * @return ApiInterface
     */
    public function api($name)
    {
        switch ($name) {
            case Api::ORDERS:
                $api = new Api\Order($this);
                break;
            case Api::BRANDS:
                $api = new Api\Brand($this);
                break;
            case Api::REASONS:
                $api = new Api\Reason($this);
                break;
            case Api::PRODUCTS:
                $api = new Api\Product($this);
                break;
            default:
                throw new InvalidArgumentException(
                    sprintf(
                        'Undefined api instance called: "%s"',
                        $name
                    )
                );
        }

        return $api;
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        if (null === $this->httpClient) {
            $this->httpClient = new HttpClient($this->client_id, $this->client_secret);
        }

        return $this->httpClient;
    }

    /**
     * @param HttpClientInterface $httpClient
     */
    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Clears used headers.
     */
    public function clearHeaders()
    {
        $this->getHttpClient()
            ->clearHeaders()
        ;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->getHttpClient()
            ->setHeaders($headers)
        ;
    }

    /**
     * Returns an array of valid API versions supported by this client.
     *
     * @return array
     */
    public function getSupportedApiVersions()
    {
        return ApiVersions::all();
    }

    /**
     * Returns an array of valid APIs supported by this client.
     *
     * @return array
     */
    public function getApiList()
    {
        return Api::all();
    }

    /**
     * @param string $name
     * @param string $args
     *
     * @throws InvalidArgumentException
     *
     * @return ApiInterface
     */
    public function __call($name, $args)
    {
        try {
            return $this->api($name);
        } catch (InvalidArgumentException $e) {
            throw new BadMethodCallException(
                sprintf(
                    'Undefined method called: "%s"',
                    $name
                )
            );
        }
    }
}
