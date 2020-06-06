<?php

declare(strict_types=1);

namespace App\Services\Sync;

use App\Exceptions\ImportException;
use App\Services\Configuration\Configuration;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class RoutineManager.
 */
class RoutineManager
{
    private array                $allErrors;
    private array                $allMessages;
    private array                $allWarnings;
    private Configuration        $configuration;
    private string               $downloadIdentifier;
    private ParseSpectreDownload $spectreParser;
    private string               $syncIdentifier;
    private GenerateTransactions $transactionGenerator;
    private FilterTransactions   $transactionFilter;
    private SendTransactions     $transactionSender;

    /**
     * Collect info on the current job, hold it in memory.
     *
     * TODO the action to start or find the sync job should not be in the constructor but in the start() method.
     *
     * ImportRoutineManager constructor.
     *
     * @param null|string $syncIdentifier
     */
    public function __construct(?string $syncIdentifier = null)
    {
        app('log')->debug('Constructed RoutineManager for sync');

        $this->spectreParser        = new ParseSpectreDownload;
        $this->transactionGenerator = new GenerateTransactions;
        $this->transactionFilter    = new FilterTransactions;
        $this->transactionSender    = new SendTransactions;

        // get line converter
        $this->allMessages = [];
        $this->allWarnings = [];
        $this->allErrors   = [];
        if (null === $syncIdentifier) {
            $this->generateSyncIdentifier();
        }
        if (null !== $syncIdentifier) {
            $this->syncIdentifier = $syncIdentifier;
        }
        $this->spectreParser->setIdentifier($this->syncIdentifier);
        $this->transactionGenerator->setIdentifier($this->syncIdentifier);
        $this->transactionFilter->setIdentifier($this->syncIdentifier);
        $this->transactionSender->setIdentifier($this->syncIdentifier);
    }

    /**
     * @return array
     */
    public function getAllErrors(): array
    {
        return $this->allErrors;
    }

    /**
     * @return array
     */
    public function getAllMessages(): array
    {
        return $this->allMessages;
    }

    /**
     * @return array
     */
    public function getAllWarnings(): array
    {
        return $this->allWarnings;
    }

    /**
     * @return string
     */
    public function getDownloadIdentifier(): string
    {
        return $this->downloadIdentifier;
    }

    /**
     * @param string $downloadIdentifier
     */
    public function setDownloadIdentifier(string $downloadIdentifier): void
    {
        $this->downloadIdentifier = $downloadIdentifier;
    }

    /**
     * @return string
     */
    public function getSyncIdentifier(): string
    {
        return $this->syncIdentifier;
    }

    /**
     * @param string $syncIdentifier
     */
    public function setSyncIdentifier(string $syncIdentifier): void
    {
        $this->syncIdentifier = $syncIdentifier;
    }

    /**
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration): void
    {
        $this->configuration = $configuration;
        $this->transactionGenerator->setConfiguration($configuration);
        $this->transactionSender->setConfiguration($configuration);
    }

    /**
     * Start the import.
     *
     * @throws ImportException
     */
    public function start(): void
    {
        app('log')->debug(sprintf('Now in %s', __METHOD__));

        // get JSON file from Spectre download
        app('log')->debug('Going to parse spectre download.');
        $array = $this->spectreParser->getDownload($this->downloadIdentifier);
        app('log')->debug('Done parsing spectre download.');

        // generate Firefly III ready transactions:
        app('log')->debug('Generating Firefly III transactions.');
        $this->transactionGenerator->collectTargetAccounts();

        $transactions = $this->transactionGenerator->getTransactions($array);
        app('log')->debug(sprintf('Generated %d Firefly III transactions.', count($transactions)));

        $filtered = $this->transactionFilter->filter($transactions);
        app('log')->debug(sprintf('Filtered down to %d Firefly III transactions.', count($filtered)));

        // send to Firefly III.
        app('log')->debug('Going to send them to Firefly III.');
        $sent = $this->transactionSender->send($filtered);
    }

    private function generateSyncIdentifier(): void
    {
        app('log')->debug('Going to generate sync job identifier.');
        $disk  = Storage::disk('jobs');
        $count = 0;
        do {
            $syncIdentifier = Str::random(16);
            $count++;
            app('log')->debug(sprintf('Attempt #%d results in "%s"', $count, $syncIdentifier));
        } while ($count < 30 && $disk->exists($syncIdentifier));
        $this->syncIdentifier = $syncIdentifier;
        app('log')->info(sprintf('Sync job identifier is "%s"', $syncIdentifier));
    }
}
