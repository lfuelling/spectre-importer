<?php

declare(strict_types=1);


namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Services\Configuration\Configuration;
use App\Services\Session\Constants;
use GrumpyDictator\FFIIIApiSupport\Exceptions\ApiHttpException;
use GrumpyDictator\FFIIIApiSupport\Request\GetAccountsRequest;
use GrumpyDictator\FFIIIApiSupport\Response\GetAccountsResponse;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Class MappingController.
 */
class MappingController extends Controller
{
    /**
     * MappingController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        app('view')->share('pageTitle', 'Map your Spectre data to Firefly III');
    }

    /**
     * @throws ApiHttpException
     */
    public function index()
    {
        $mainTitle = 'Map data';
        $subTitle  = 'Link Spectre information to Firefly III data.';

        $configuration = Configuration::fromArray([]);
        if (session()->has(Constants::CONFIGURATION)) {
            $configuration = Configuration::fromArray(session()->get(Constants::CONFIGURATION));
        }
        // if config says to skip it, skip it:
        if (null !== $configuration && false === $configuration->isDoMapping()) {
            // skipForm
            return redirect()->route('import.sync.index');
        }

        $mapping = $configuration->getMapping();

        // parse all opposing accounts from the download
        $spectreAccounts = $this->getOpposingAccounts();

        // get accounts from Firefly III
        $ff3Accounts = $this->getFireflyIIIAccounts();

        return view('import.mapping.index', compact('mainTitle', 'subTitle', 'configuration', 'spectreAccounts', 'ff3Accounts', 'mapping'));
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @psalm-return RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postIndex(Request $request)
    {
        // post mapping is not particularly complex.
        $result       = $request->all();
        $mapping      = $result['mapping'] ?? [];
        $accountTypes = $result['account_type'] ?? [];

        $configuration = Configuration::fromArray([]);
        if (session()->has(Constants::CONFIGURATION)) {
            $configuration = Configuration::fromArray(session()->get(Constants::CONFIGURATION));
        }
        // if config says to skip it, skip it:
        if (null !== $configuration && false === $configuration->isDoMapping()) {
            // skipForm
            return redirect()->route('import.sync.index');
        }
        // save mapping in config.
        $configuration->setMapping($mapping);
        $configuration->setAccountTypes($accountTypes);

        // save mapping in config, save config.
        session()->put(Constants::CONFIGURATION, $configuration->toArray());

        return redirect(route('import.sync.index'));
    }

    /**
     * @throws ApiHttpException
     * @return array
     */
    private function getFireflyIIIAccounts(): array
    {
        $token   = (string) config('spectre.access_token');
        $uri     = (string) config('spectre.uri');
        $request = new GetAccountsRequest($uri, $token);
        /** @var GetAccountsResponse $result */
        $result = $request->get();
        $return = [];
        foreach ($result as $entry) {
            $type = $entry->type;
            if ('reconciliation' === $type || 'initial-balance' === $type) {
                continue;
            }
            $id                 = (int) $entry->id;
            $return[$type][$id] = $entry->name;
            if ('' !== (string) $entry->iban) {
                $return[$type][$id] = sprintf('%s (%s)', $entry->name, $entry->iban);
            }
        }
        foreach ($return as $type => $entries) {
            asort($return[$type]);
        }

        return $return;
    }

    /**
     * @throws FileNotFoundException
     * @return array
     */
    private function getOpposingAccounts(): array
    {
        $downloadIdentifier = session()->get(Constants::DOWNLOAD_JOB_IDENTIFIER);
        $disk               = Storage::disk('downloads');
        $json               = $disk->get($downloadIdentifier);
        $array              = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $opposing           = [];
        /** @var array $account */
        foreach ($array as $account) {
            foreach ($account as $entry) {
                if ('' === trim((string) $entry['counter_party']['iban'])) {
                    $opposing[] = trim($entry['counter_party']['display_name']);
                }
                if ('' !== trim((string) $entry['counter_party']['iban'])) {
                    $opposing[] = sprintf('%s (%s)', trim($entry['counter_party']['display_name']), trim($entry['counter_party']['iban']));
                }
            }
        }
        $filtered = array_filter(
            $opposing,
            static function (string $value) {
                return '' !== $value;
            }
        );

        return array_unique($filtered);
    }
}
