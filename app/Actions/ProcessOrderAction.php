<?php

namespace App\Actions;

use App\Models\Order;
use Spatie\QueueableAction\QueueableAction;
use Illuminate\Support\Facades\Log;

class ProcessOrderAction
{
    use QueueableAction;

    /**
     * Execute the action
     */
    public function execute(Order $order, array $options = [])
    {
        // Simulate processing time
        sleep(5);
        
        // Update order status
        $order->status = 'processed';
        $order->notes = ($order->notes ? $order->notes . "\n" : '') 
            . 'Processed at: ' . now()->toDateTimeString();
        
        if (isset($options['priority'])) {
            $order->notes .= ' (Priority: ' . $options['priority'] . ')';
        }
        
        $order->save();
        
        // Log the action
        Log::info('Order processed successfully', [
            'order_id' => $order->id,
            'customer' => $order->customer_name,
            'amount' => $order->total_amount,
            'options' => $options
        ]);
        
        return $order;
    }
    
    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception, Order $order, array $options = [])
    {
        Log::error('Failed to process order', [
            'order_id' => $order->id,
            'error' => $exception->getMessage()
        ]);
        
        $order->status = 'failed';
        $order->notes = ($order->notes ? $order->notes . "\n" : '') 
            . 'Failed at: ' . now()->toDateTimeString() 
            . ' - Error: ' . $exception->getMessage();
        $order->save();
    }
}