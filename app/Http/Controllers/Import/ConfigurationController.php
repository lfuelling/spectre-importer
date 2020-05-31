<?php
/**
 * ConfigurationController.php
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
use App\Services\Configuration\Configuration;
use App\Services\Session\Constants;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use JsonException;
use Log;

/**
 * Class ConfigurationController
 */
class ConfigurationController extends Controller
{
    /**
     * @return ResponseFactory|Response
     */
    public function download()
    {
        // do something
        $result = '';
        $config = Configuration::fromArray(session()->get(Constants::CONFIGURATION))->toArray();
        try {
            $result = json_encode($config, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT, 512);
        } catch (JsonException $e) {
            Log::error($e->getMessage());
        }

        $response = response($result);
        $name     = sprintf('spectre_import_config_%s.json', date('Y-m-d'));
        $response->header('Content-disposition', 'attachment; filename=' . $name)
                 ->header('Content-Type', 'application/json')
                 ->header('Content-Description', 'File Transfer')
                 ->header('Connection', 'Keep-Alive')
                 ->header('Expires', '0')
                 ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                 ->header('Pragma', 'public')
                 ->header('Content-Length', strlen($result));

        return $response;
    }
}
