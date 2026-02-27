<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queueable Actions Demo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Queueable Actions Demo</h1>
        
        <!-- Create Order Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Create Test Order</h2>
            <form id="createOrderForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Customer Name</label>
                    <input type="text" id="customerName" value="Test Customer" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                    <input type="number" id="totalAmount" value="250.00" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Create Order
                </button>
            </form>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Actions</h2>
            <div class="space-x-4">
                <button id="processBulkBtn" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                    Process Bulk Orders (Queued)
                </button>
                <button id="refreshOrdersBtn" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Refresh Orders
                </button>
            </div>
        </div>

        <!-- Orders List -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Orders</h2>
            <div id="ordersList" class="space-y-4">
                <!-- Orders will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        // Load orders
        async function loadOrders() {
            const response = await fetch('/orders');
            const data = await response.json();
            
            const ordersList = document.getElementById('ordersList');
            ordersList.innerHTML = data.orders.map(order => `
                <div class="border rounded p-4 ${order.status === 'processed' ? 'bg-green-50' : order.status === 'failed' ? 'bg-red-50' : 'bg-yellow-50'}">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold">${order.customer_name}</h3>
                            <p class="text-sm text-gray-600">Amount: $${order.total_amount}</p>
                            <p class="text-sm">Status: <span class="font-medium">${order.status}</span></p>
                            <p class="text-xs text-gray-500">Created: ${new Date(order.created_at).toLocaleString()}</p>
                            ${order.notes ? `<p class="text-xs text-gray-600 mt-2">${order.notes.replace(/\n/g, '<br>')}</p>` : ''}
                        </div>
                        <div class="space-x-2">
                            <button onclick="processOrderSync(${order.id})" class="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">
                                Process Sync
                            </button>
                            <button onclick="processOrderQueued(${order.id})" class="text-xs bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">
                                Process Queued
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Create order
        document.getElementById('createOrderForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const response = await fetch('/orders/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    customer_name: document.getElementById('customerName').value,
                    total_amount: document.getElementById('totalAmount').value
                })
            });
            
            if (response.ok) {
                loadOrders();
            }
        });

        // Process single order synchronously
        async function processOrderSync(id) {
            await fetch(`/orders/${id}/process-sync`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            loadOrders();
        }

        // Process single order queued
        async function processOrderQueued(id) {
            await fetch(`/orders/${id}/process-queued`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            alert('Order queued for processing! Check back in a few seconds.');
            setTimeout(loadOrders, 5000);
        }

        // Process bulk orders
        document.getElementById('processBulkBtn').addEventListener('click', async () => {
            await fetch('/orders/process-bulk', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            alert('Bulk orders queued for processing! Check back in a few seconds.');
            setTimeout(loadOrders, 10000);
        });

        // Refresh orders
        document.getElementById('refreshOrdersBtn').addEventListener('click', loadOrders);

        // Initial load
        loadOrders();
        // Auto refresh every 5 seconds
        setInterval(loadOrders, 5000);
    </script>
</body>
</html>