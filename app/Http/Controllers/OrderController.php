<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Actions\ProcessOrderAction;
use App\Actions\ProcessBulkOrdersAction;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected ProcessOrderAction $processOrderAction;
    protected ProcessBulkOrdersAction $processBulkOrdersAction;

    public function __construct(
        ProcessOrderAction $processOrderAction,
        ProcessBulkOrdersAction $processBulkOrdersAction
    ) {
        $this->processOrderAction = $processOrderAction;
        $this->processBulkOrdersAction = $processBulkOrdersAction;
    }

    /**
     * Process a single order (sync)
     */
    public function processSync(Order $order)
    {
        $result = $this->processOrderAction->execute($order);
        
        return response()->json([
            'message' => 'Order processed synchronously',
            'order' => $result
        ]);
    }

    /**
     * Process a single order (queued)
     */
    public function processQueued(Order $order)
    {
        $this->processOrderAction->onQueue()->execute($order, [
            'priority' => 'high'
        ]);
        
        return response()->json([
            'message' => 'Order queued for processing',
            'order' => $order
        ]);
    }

    /**
     * Process bulk orders (queued with chain)
     */
    public function processBulkQueued()
    {
        $pendingOrders = Order::where('status', 'pending')
            ->limit(5)
            ->get();
        
        // Queue the bulk action
        $this->processBulkOrdersAction
            ->onQueue()
            ->execute($pendingOrders, ['priority' => 'bulk']);
        
        return response()->json([
            'message' => 'Bulk orders queued for processing',
            'orders_count' => $pendingOrders->count()
        ]);
    }

    /**
     * Create a test order
     */
    public function createTestOrder(Request $request)
    {
        $order = Order::create([
            'customer_name' => $request->input('customer_name', 'Test Customer'),
            'total_amount' => $request->input('total_amount', rand(100, 1000)),
            'status' => 'pending'
        ]);
        
        return response()->json([
            'message' => 'Test order created',
            'order' => $order
        ]);
    }

    /**
     * List all orders
     */
    public function index()
    {
        $orders = Order::latest()->get();
        
        return response()->json([
            'orders' => $orders
        ]);
    }
}