<?php
declare(strict_types=1);


namespace App\Services\Spectre\Response;

/**
 * Class Response
 */
abstract class Response
{
    /**
     * Response constructor.
     *
     * @param array $data
     */
    abstract public function __construct(array $data);

}
