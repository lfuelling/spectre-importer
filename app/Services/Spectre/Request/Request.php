<?php
declare(strict_types=1);


namespace App\Services\Spectre\Request;

use App\Exceptions\SpectreErrorException;
use App\Exceptions\SpectreHttpException;
use App\Services\Spectre\Response\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use JsonException;
use Log;

/**
 * Class Request
 */
abstract class Request
{
    /** @var string */
    private $base;
    /** @var array */
    private $body;
    /** @var array */
    private $parameters;
    /** @var string */
    private $uri;

    /** @var string */
    private $appId;
    /** @var string */
    private $secret;

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * @param string $appId
     */
    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }


    /**
     * @param string $base
     */
    public function setBase(string $base): void
    {
        $this->base = $base;
    }

    /**
     * @param array $body
     */
    public function setBody(array $body): void
    {
        $this->body = $body;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @param string $uri
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * @return string
     */
    public function getBase(): string
    {
        return $this->base;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @throws SpectreHttpException
     * @return Response
     */
    abstract public function get(): Response;

    /**
     * @throws SpectreErrorException
     * @throws SpectreHttpException
     * @return array
     */
    protected function authenticatedGet(): array
    {
        $fullUri = sprintf('%s/%s', $this->getBase(), $this->getUri());
        if (null !== $this->parameters) {
            $fullUri = sprintf('%s?%s', $fullUri, http_build_query($this->parameters));
        }
        $client = $this->getClient();
        $res    = null;
        $body   = null;
        $json   = null;
        try {
            $res = $client->request(
                'GET', $fullUri, [
                         'headers' => [
                             'Accept'       => 'application/json',
                             'Content-Type' => 'application/json',
                             'App-id'       => $this->getAppId(),
                             'Secret'       => $this->getSecret(),
                         ],
                     ]
            );
        } catch (TransferException $e) {
            Log::error(sprintf('TransferException: %s', $e->getMessage()));
            // if response, parse as error response.re
            if (!$e->hasResponse()) {
                throw new SpectreHttpException(sprintf('Exception: %s', $e->getMessage()));
            }
            $body = (string) $e->getResponse()->getBody();
            $json = [];
            try {
                $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                Log::error(sprintf('Could not decode error: %s', $e->getMessage()));
            }

            $exception       = new SpectreErrorException;
            $exception->json = $json;
            throw $exception;
        }
        if (null !== $res && 200 !== $res->getStatusCode()) {
            // return body, class must handle this
            Log::error(sprintf('Status code is %d: %s', $res->getStatusCode(), $e->getMessage()));

            $body = (string) $res->getBody();
        }
        $body = $body ?? (string) $res->getBody();

        try {
            $json = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new SpectreHttpException(
                sprintf(
                    'Could not decode JSON (%s). Error[%d] is: %s. Response: %s',
                    $fullUri,
                    $res ? $res->getStatusCode() : 0,
                    $e->getMessage(),
                    $body
                )
            );
        }

        if (null === $json) {
            throw new SpectreHttpException(sprintf('Body is empty. Status code is %d.', $res->getStatusCode()));
        }

        return $json;
    }

    /**
     * @throws SpectreHttpException
     * @return Response
     */
    abstract public function put(): Response;

    /**
     * @throws SpectreHttpException
     * @return Response
     */
    abstract public function post(): Response;

    /**
     * @return Client
     */
    private function getClient(): Client
    {
        // config here

        return new Client;
    }
}
