<?php

declare(strict_types=1);

namespace App\Console;

use Exception;
use JsonException;

/**
 * Trait VerifyJSON.
 */
trait VerifyJSON
{
    /**
     * @param string $file
     *
     * @return bool
     */
    private function verifyJSON(string $file): bool
    {
        // basic check on the JSON.
        $json = file_get_contents($file);
        try {
            $configuration = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception | JsonException $e) {
            $message = sprintf('The importer can\'t import: could not decode the JSON in the config file: %s', $e->getMessage());
            app('log')->error($message);

            return false;
        }

        return true;
    }
}
