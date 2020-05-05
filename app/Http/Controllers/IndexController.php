<?php

declare(strict_types=1);

namespace App\Http\Controllers;
use Artisan;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Log;

/**
 *
 * Class IndexController
 */
class IndexController extends Controller
{
    /**
     * IndexController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        app('view')->share('pageTitle', 'Index');
    }

    /**
     * @return Factory|View
     */
    public function index()
    {
        Log::debug(sprintf('Now at %s', __METHOD__));

        return view('index');
    }

    /**
     * @return RedirectResponse|Redirector
     */
    public function flush()
    {
        Log::debug(sprintf('Now at %s', __METHOD__));
        session()->flush();
        Artisan::call('cache:clear');

        return redirect(route('index'));
    }

}
