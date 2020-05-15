<?php
declare(strict_types=1);


namespace App\Services\Spectre\Request;

use App\Exceptions\SpectreErrorException;
use App\Exceptions\SpectreHttpException;
use App\Services\Spectre\Response\ErrorResponse;
use App\Services\Spectre\Response\Response;
use JsonException;

/**
 * Class ListCustomersRequest
 */
class ListCustomersRequest extends Request
{
    /**
     * ListCustomersRequest constructor.
     *
     * @param string $url
     * @param string $token
     */
    public function __construct(string $url, string $appId, string $secret)
    {
        $this->type = 'all';
        $this->setBase($url);
        $this->setAppId($appId);
        $this->setSecret($secret);
        $this->setUri('customers');
    }

    /**
     * @inheritDoc
     * @throws JsonException
     */
    public function get(): Response
    {
        try {
            $response = $this->authenticatedGet();
        } catch (SpectreHttpException $e) {
            var_dump($e->getMessage());
        } catch (SpectreErrorException $e) {
            // JSON thing.
            return new ErrorResponse($e->json ?? []);
        }
        var_dump($response);
        exit;
    }

    /**
     * @inheritDoc
     */
    public function put(): Response
    {
        // TODO: Implement put() method.
    }

    /**
     * @inheritDoc
     */
    public function post(): Response
    {
        // TODO: Implement post() method.
    }
}
