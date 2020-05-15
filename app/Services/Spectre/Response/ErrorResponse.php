<?php
declare(strict_types=1);


namespace App\Services\Spectre\Response;


/**
 * Class ErrorResponse
 */
class ErrorResponse extends Response
{
    public $class;
    public $message;

    /**
     * @inheritDoc
     */
    public function __construct(array $data)
    {
        $this->class = $data['error']['class'] ?? 'Unknown Spectre Error Class';
        $this->message = $data['error']['message'] ?? 'Unknown Error';
    }
}
