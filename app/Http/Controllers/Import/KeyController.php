<?php
/**
 * KeyController.php
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
use App\Services\Local\VerifyKeyMaterial;
use App\Services\Session\Constants;
use App\Services\Storage\StorageService;
use Illuminate\Http\Request;
use JsonException;
use Log;

/**
 * Class KeyController
 */
class KeyController extends Controller
{
    /**
     * @throws \App\Exceptions\ImportException
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        VerifyKeyMaterial::verifyOrCreate();
        $publicKey = VerifyKeyMaterial::getPublicKey();

        // list customers. Create new one if necessary.
        $mainTitle = 'Key material';
        $subTitle  = 'Required for security';

        return view('import.key.index', compact('publicKey', 'mainTitle', 'subTitle'));
    }

    /**
     * @param Request $request
     */
    public function post(Request $request)
    {
        // skip next time?
        $configuration = Configuration::fromArray([]);
        if (session()->has(Constants::CONFIGURATION)) {
            $configuration = Configuration::fromArray(session()->get(Constants::CONFIGURATION));
        }
        $configuration->setSkipKey(false);
        if ('1' === $request->get('skip_key')) {
            $configuration->setSkipKey(true);
        }

        // save config
        $json = '[]';
        try {
            $json = json_encode($configuration, JSON_THROW_ON_ERROR, 512);
        } catch (JsonException $e) {
            Log::error($e->getMessage());
        }
        StorageService::storeContent($json);

        session()->put(Constants::CONFIGURATION, $configuration->toArray());

        // redirect to list of logins:
        return redirect(route('import.connections.index'));
    }

}
