<?php
declare(strict_types=1);


namespace App\Services\Sync;

use App\Services\Sync\JobStatus\ProgressInformation;
use Log;

/**
 * Class FilterTransactions
 */
class FilterTransactions
{
    use ProgressInformation;

    /**
     * FilterTransactions constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param array $transactions
     *
     * @return array
     */
    public function filter(array $transactions): array
    {
        $start  = count($transactions);
        $return = [];
        /** @var array $transaction */
        foreach ($transactions as $transaction) {

            unset($transaction['transactions'][0]['datetime']);

            if (0 === (int) ($transaction['transactions'][0]['category_id'] ?? 0)) {
                Log::debug('IS NULL');
                unset($transaction['transactions'][0]['category_id']);
            }
            $return[] = $transaction;
            Log::debug('Filtered ', $transaction);
        }
        $end = count($return);
        $this->addMessage(0, sprintf('Filtered down from %d (possibly duplicate) entries to %d unique transactions.', $start, $end));

        return $return;
    }

}
