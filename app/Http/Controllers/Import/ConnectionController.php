<?php
/**
 * ConnectionController.php
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


use App\Exceptions\ImportException;
use App\Http\Controllers\Controller;
use App\Services\Spectre\Model\Customer;
use App\Services\Spectre\Request\ListConnectionsRequest;
use App\Services\Spectre\Request\ListCustomersRequest;
use App\Services\Spectre\Request\PostConnectSessionsRequest;
use App\Services\Spectre\Request\PostCustomerRequest;
use App\Services\Spectre\Response\ErrorResponse;
use App\Services\Spectre\Response\PostCustomerResponse;
use Illuminate\Http\Request;
use Log;

/**
 * Class ConnectionController
 */
class ConnectionController extends Controller
{
    /**
     *
     */
    public function index()
    {
        $mainTitle = 'Spectre';
        $subTitle  = 'Select your financial organisation';
        $uri       = config('spectre.spectre_uri');
        $appId     = config('spectre.spectre_app_id');
        $secret    = config('spectre.spectre_secret');

        // check if already has the correct customer:
        $hasCustomer = false;
        $request     = new ListCustomersRequest($uri, $appId, $secret);
        $list        = $request->get();
        $identifier  = null;

        if ($list instanceof ErrorResponse) {
            throw new ImportException(sprintf('%s: %s', $list->class, $list->message));
        }
        /** @var Customer $item */
        foreach ($list as $item) {
            if ('default_ff3_customer' === $item->identifier) {
                $hasCustomer = true;
                $identifier  = $item->id;
            }
        }

        if (false === $hasCustomer) {
            // create new one
            $request             = new PostCustomerRequest($uri, $appId, $secret);
            $request->identifier = 'default_ff3_customer';
            /** @var PostCustomerResponse $customer */
            $customer   = $request->post();
            $identifier = $customer->customer->id;

        }
        Log::debug('About to get connections.');
        $request           = new ListConnectionsRequest($uri, $appId, $secret);
        $request->customer = (string) $identifier;
        $list              = $request->get();

        if ($list instanceof ErrorResponse) {
            throw new ImportException(sprintf('%s: %s', $list->class, $list->message));
        }

        return view('import.connection.index', compact('mainTitle', 'subTitle', 'list'));
    }

    /**
     * @param Request $request
     */
    public function post(Request $request)
    {
        $connectionId = $request->get('spectre_connection_id');
        if ('00' === $connectionId) {
            // make a new connection.
            // post to https://www.saltedge.com/api/v5/connect_sessions/create
            $uri      = config('spectre.spectre_uri');
            $appId    = config('spectre.spectre_app_id');
            $secret   = config('spectre.spectre_secret');
            $newToken = new PostConnectSessionsRequest($uri, $appId, $secret);
            $newToken->post();
            echo '1234';
            exit;
        }
        var_dump($request->all());
        exit;

    }

}
