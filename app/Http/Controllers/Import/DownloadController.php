<?php
/**
 * DownloadController.php
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

namespace App\Http\Controllers\Import;


use App\Http\Controllers\Controller;
use App\Services\Session\Constants;
use App\Services\Spectre\Download\RoutineManager;

/**
 * Class DownloadController
 */
class DownloadController extends Controller
{
    /**
     * DownloadController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        app('view')->share('pageTitle', 'Download transactions from Spectre');
    }

    public function index()
    {
        $mainTitle = 'Downloading transactions...';
        $subTitle  = 'Connecting to Spectre and downloading your data...';
        $routine = null;
        // job ID may be in session:
        $downloadIdentifier = session()->get(Constants::DOWNLOAD_JOB_IDENTIFIER);
        if (null === $downloadIdentifier) {
            // create a new download job:
            $routine            = new RoutineManager;
            $downloadIdentifier = $routine->getDownloadIdentifier();
        }

        // call thing:
        JobStatusManager::startOrFindJob($downloadIdentifier);

        app('log')->debug(sprintf('Download routine manager identifier is "%s"', $downloadIdentifier));

        // store identifier in session so the status can get it.
        session()->put(Constants::DOWNLOAD_JOB_IDENTIFIER, $downloadIdentifier);
        app('log')->debug(sprintf('Stored "%s" under "%s"', $downloadIdentifier, Constants::DOWNLOAD_JOB_IDENTIFIER));

        return view('import.download.index', compact('mainTitle', 'subTitle', 'downloadIdentifier'));
    }
}
