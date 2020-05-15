<?php
declare(strict_types=1);


namespace App\Exceptions;

use Exception;

/**
 * Class SpectreErrorException
 */
class SpectreErrorException extends Exception
{
    /** @var array */
    public $json;
}
