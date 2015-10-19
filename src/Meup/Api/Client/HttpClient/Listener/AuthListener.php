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

use Guzzle\Common\Event;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;
use HomeApi\Client\Exception\AuthenticationException;
use HomeApi\Client\MeupApiClient;
use HomeApi\Client\Exception\RuntimeException;

/**
 * Class AuthListener
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class AuthListener
{
    private $client;
    private $api_base_uri;
    private $client_id;
    private $secret_key;
    private $method;

    public function __construct(Client $client, $apiBaseUri, $clientId, $secretKey, $method = null)
    {
        $this->client       = $client;
        $this->api_base_uri = $apiBaseUri;
        $this->client_id    = $clientId;
        $this->secret_key   = $secretKey;
        $this->method       = $method;
    }

    public function onRequestBeforeSend(Event $event)
    {
        if (null === $this->method) {
            return;
        }

        switch ($this->method) {
            case 'client_credentials':
                if (null !== $event['request']->getHeader('Authorization')) {
                    break;
                }

                $oauthQueryString  = "/oauth/v2/token?client_id=%s&client_secret=%s&grant_type=%s";
                $requestTokenUri   =
                    rtrim($this->api_base_uri, '/') .
                    sprintf(
                        $oauthQueryString,
                        $this->client_id,
                        $this->secret_key,
                        'client_credentials'
                    )
                ;

                try {
                    $token = $this->requestClientCredentialToken($requestTokenUri);
                    $event['request']->setHeader('Authorization', "Bearer {$token}");
                } catch (ClientErrorResponseException $e) {
                    if (strstr($e->getMessage(), '[status code] 400')) {
                        $message = 'Invalid credentials';
                        throw new AuthenticationException($message);
                    }
                    throw $e;
                }
                break;
            default:
                throw new RuntimeException(sprintf('%s not yet implemented', $this->method));
                break;
        }
    }

    /**
     * @param $requestTokenUri
     *
     * @return string
     */
    private function requestClientCredentialToken($requestTokenUri)
    {
        $this->client->setSslVerification(false, false);
        $request = $this->client->get($requestTokenUri);
        $response = $request->send();
        $data = $response->json();
        if (!array_key_exists('access_token', $data)) {
            $message = 'No access token returned on authenticate';
            throw new AuthenticationException($message);
        } else {
            return $data['access_token'];
        }
    }
}
