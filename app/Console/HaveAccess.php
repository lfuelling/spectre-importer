<?php

declare(strict_types=1);

namespace App\Console;

use GrumpyDictator\FFIIIApiSupport\Exceptions\ApiHttpException;
use GrumpyDictator\FFIIIApiSupport\Request\SystemInformationRequest;

/**
 * Trait HaveAccess.
 */
trait HaveAccess
{
    /**
     * @return bool
     */
    private function haveAccess(): bool
    {
        $uri     = (string) config('spectre.uri');
        $token   = (string) config('spectre.access_token');
        $request = new SystemInformationRequest($uri, $token);
        try {
            $request->get();
        } catch (ApiHttpException $e) {
            $this->error(sprintf('Could not connect to Firefly III: %s', $e->getMessage()));

            return false;
        }

        return true;
    }
}
