<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Artisan;

/**
 * Class Controller
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * Controller constructor.
     */
    public function __construct()
    {

        $variables = [
            'FIREFLY_III_ACCESS_TOKEN' => 'spectre.access_token',
            'FIREFLY_III_URI'          => 'spectre.uri',
        ];
        foreach ($variables as $env => $config) {

            $value = (string)config($config);
            if ('' === $value) {
                echo sprintf('Please set a valid value for "%s" in the env file.', $env);
                Artisan::call('config:clear');
                exit;
            }
        }
        $path     = config('spectre.upload_path');
        $writable = is_dir($path) && is_writable($path);
        if (false === $writable) {
            echo sprintf('Make sure that directory "%s" exists and is writeable.', $path);
            exit;
        }

        app('view')->share('version', config('spectre.version'));
    }
}
