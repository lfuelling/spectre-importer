<?php
/**
 * RoutineManager.php
 * Copyright (c) 2020 james@firefly-iii.org
 *
 * This file is part of the Firefly III Spectre importer
 * (https://github.com/firefly-iii/spectre-importer).
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Services\Spectre\Download;

use Storage;
use Str;

/**
 * Class RoutineManager
 */
class RoutineManager
{
    private string $downloadIdentifier;
    private array $allErrors;
    private array $allMessages;
    private array $allWarnings;
    /**
     * RoutineManager constructor.
     *
     * @param string|null $downloadIdentifier
     */
    public function __construct(string $downloadIdentifier = null) {
        app('log')->debug('Constructed Spectre download routine manager.');

        // get line converter
        $this->allMessages = [];
        $this->allWarnings = [];
        $this->allErrors   = [];
        if (null === $downloadIdentifier) {
            app('log')->debug('Was given no download identifier, will generate one.');
            $this->generateDownloadIdentifier();
        }
        if (null !== $downloadIdentifier) {
            app('log')->debug('Was given download identifier, will use it.');
            $this->downloadIdentifier = $downloadIdentifier;
        }
    }

    /**
     * @return string
     */
    public function getDownloadIdentifier(): string
    {
        return $this->downloadIdentifier;
    }

    /**
     *
     */
    private function generateDownloadIdentifier(): void
    {
        app('log')->debug('Going to generate download identifier.');
        $disk  = Storage::disk('download_jobs');
        $count = 0;
        do {
            $downloadIdentifier = Str::random(16);
            $count++;
            app('log')->debug(sprintf('Attempt #%d results in "%s"', $count, $downloadIdentifier));
        } while ($count < 30 && $disk->exists($downloadIdentifier));
        $this->downloadIdentifier = $downloadIdentifier;
        app('log')->info(sprintf('Download job identifier is "%s"', $downloadIdentifier));
    }
}
