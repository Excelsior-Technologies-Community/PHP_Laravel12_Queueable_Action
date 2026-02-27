<?php

namespace App\Actions;

use App\Models\Order;
use Spatie\QueueableAction\QueueableAction;
use Illuminate\Support\Collection;

class ProcessBulkOrdersAction
{
    use QueueableAction;

    protected ProcessOrderAction $processOrderAction;

    public function __construct(ProcessOrderAction $processOrderAction)
    {
        $this->processOrderAction = $processOrderAction;
    }

    /**
     * Execute the action
     */
    public function execute(Collection $orders, array $options = [])
    {
        $results = [];
        
        foreach ($orders as $order) {
            // Execute the ProcessOrderAction
            $result = $this->processOrderAction->execute($order, $options);
            $results[] = $result;
        }
        
        return [
            'processed_count' => count($results),
            'total_amount' => $results->sum('total_amount'),
            'orders' => $results
        ];
    }
}