<?php
declare(strict_types=1);


namespace App\Services\Spectre\Request;

/**
 * Class Request
 */
class Request
{
    /** @var string */
    private $base;
    /** @var array */
    private $body;
    /** @var array */
    private $parameters;
    /** @var string */
    private $token;
    /** @var string */
    private $uri;

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
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @param string $uri
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }



}
