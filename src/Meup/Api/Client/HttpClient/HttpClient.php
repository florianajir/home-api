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

use Guzzle\Http\Client;
use Guzzle\Http\Exception\CurlException;
use HomeApi\Client\Exception\ApiNotRespondingException;
use HomeApi\Client\Exception\ErrorException;
use HomeApi\Client\Exception\RuntimeException;
use HomeApi\Client\HttpClient\Listener\AuthListener;
use HomeApi\Client\HttpClient\Listener\ErrorListener;
use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Performs requests on florianajir API. API documentation should be self-explanatory.
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class HttpClient implements HttpClientInterface
{
    protected $client_id;
    protected $client_secret;
    protected $access_token;

    protected $options = array(
        'base_url'    => 'https://api.florianajir.com',

        'user_agent'  => 'php-meup-api (http://github.com/florianajir/home-api)',
        'timeout'     => 10,

        'api_limit'   => 5000,
        'api_version' => '1.0',

        'cache_dir'   => null
    );

    protected $headers = array();

    private $lastResponse;
    private $lastRequest;

    /**
     * @param string          $client_id
     * @param string          $client_secret
     * @param array           $options
     * @param ClientInterface $client
     */
    public function __construct(
        $client_id,
        $client_secret,
        array $options = array(),
        ClientInterface $client = null
    ) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->options = array_merge($this->options, $options);
        $client = $client ?: new GuzzleClient($this->options['base_url'], $this->options);
        $client->setSslVerification(false, false);
        $this->client  = $client;

        $this->addListener('request.error', array(new ErrorListener($this->options), 'onRequestError'));
        $this->addListener('request.before_send', array(
            new AuthListener(
                new Client(),
                $this->options['base_url'],
                $this->client_id,
                $this->client_secret,
                'client_credentials'
            ),
            'onRequestBeforeSend'
        ));

        $this->clearHeaders();
    }

    /**
     * {@inheritDoc}
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * Update header with name => value param
     * @param string $name
     * @param string $value
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * Clears used headers.
     */
    public function clearHeaders()
    {
        $this->headers = array(
            'Accept' => sprintf('application/json;version=%s', $this->options['api_version']),
            'User-Agent' => sprintf('%s', $this->options['user_agent'])
        );
    }

    public function addListener($eventName, $listener)
    {
        $this->client->getEventDispatcher()->addListener($eventName, $listener);
    }

    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->client->addSubscriber($subscriber);
    }

    /**
     * {@inheritDoc}
     */
    public function get($path, array $parameters = array(), array $headers = array())
    {
        return $this->request($path, null, 'GET', $headers, array('query' => $parameters));
    }

    /**
     * {@inheritDoc}
     */
    public function post($path, $body = null, array $headers = array())
    {
        return $this->request($path, $body, 'POST', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function patch($path, $body = null, array $headers = array())
    {
        return $this->request($path, $body, 'PATCH', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path, $body = null, array $headers = array())
    {
        return $this->request($path, $body, 'DELETE', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function put($path, $body, array $headers = array())
    {
        return $this->request($path, $body, 'PUT', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function request(
        $path,
        $body = null,
        $httpMethod = 'GET',
        array $headers = array(),
        array $options = array()
    ) {

        try {
            $request = $this->createRequest($httpMethod, $path, $body, $headers, $options);
            $this->lastRequest  = $request;
            $response = $this->client->send($request);
            $this->lastResponse = $response;
        } catch (\LogicException $e) {
            throw new ErrorException($e->getMessage(), $e->getCode(), $e);
        } catch (CurlException $e) {
            throw new ApiNotRespondingException($e->getMessage(), $e->getCode(), $e);
        } catch (\RuntimeException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }


        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate($publicKey, $secretKey)
    {
        $this->addListener('request.before_send', array(
            new AuthListener(
                new Client(),
                $this->options['base_url'],
                $publicKey,
                $secretKey,
                'client_credentials'
            ),
            'onRequestBeforeSend'
        ));
    }

    /**
     * Clear current authentication
     */
    public function clearToken()
    {
        $this->access_token = null;
    }

    /**
     * @return Request
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * @return Response
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    protected function createRequest(
        $httpMethod,
        $path,
        $body = null,
        array $headers = array(),
        array $options = array()
    ) {
        return $this->client->createRequest(
            $httpMethod,
            rtrim($this->options['base_url'], '/') . '/' . ltrim($path, '/'),
            array_merge($this->headers, $headers),
            $body,
            $options
        );
    }
}
