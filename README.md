# PHP_Laravel12_Queueable_Action

Complete step-by-step implementation of Queueable Actions in Laravel using the spatie/laravel-queueable-action package.

This project demonstrates how to:

* Convert service classes into queueable actions
* Process tasks synchronously and asynchronously
* Chain actions together
* Handle job failures
* Use database queues
* Build a simple UI to test queued operations

---

## Project Overview

Queueable Actions allow you to write clean service classes that can be executed:

* Immediately (synchronously)
* In the background (queued)

This keeps controllers clean and improves application performance for time-consuming tasks.

---

## Prerequisites

* PHP >= 8.2
* Composer installed
* Laravel 12 installed
* Database configured

---

## Step 1: Create New Laravel Project

```bash
composer create-project laravel/laravel queueable-actions-demo
cd queueable-actions-demo
```

---

## Step 2: Install Queueable Actions Package

```bash
composer require spatie/laravel-queueable-action
```

---

## Step 3: Configure Queue Driver

For this project, the database queue driver is used.

Create queue table and run migration:

```bash
php artisan queue:table
php artisan migrate
```

Update `.env`:

```
QUEUE_CONNECTION=database
```

---

## Step 4: Create Order Model and Migration

```bash
php artisan make:model Order -m
```

Migration fields:

* id
* customer_name
* total_amount
* status (default: pending)
* notes (nullable)
* timestamps

Run migration:

```bash
php artisan migrate
```

---

## Step 5: Create ProcessOrderAction

Location:

```
app/Actions/ProcessOrderAction.php
```

Responsibilities:

* Simulates processing delay
* Updates order status to processed
* Adds processing notes
* Logs action details
* Handles failure using failed() method

Key Concepts Used:

* QueueableAction trait
* execute() method
* failed() method
* Logging

---

## Step 6: Create ProcessBulkOrdersAction

Location:

```
app/Actions/ProcessBulkOrdersAction.php
```

This action:

* Accepts a collection of orders
* Calls ProcessOrderAction internally
* Returns summary including:

  * processed_count
  * total_amount
  * processed orders list

Demonstrates:

* Action chaining
* Dependency injection inside actions

---

## Step 7: Create OrderController

Location:

```
app/Http/Controllers/OrderController.php
```

Controller Methods:

* index() – List all orders
* createTestOrder() – Create demo order
* processSync() – Process immediately
* processQueued() – Queue single order
* processBulkQueued() – Queue multiple orders

Demonstrates:

* Synchronous execution
* Asynchronous execution
* Passing parameters to queued actions

---

## Step 8: Create Routes

Add routes in `routes/web.php`:

* GET / (UI page)
* GET /orders
* POST /orders/create
* POST /orders/{order}/process-sync
* POST /orders/{order}/process-queued
* POST /orders/process-bulk

---

## Step 9: Create Simple UI

File:

```
resources/views/orders.blade.php
```

UI Features:

* Create test orders
* Process order synchronously
* Process order in queue
* Process bulk orders
* Auto-refresh order list
* Status highlighting

Frontend built using:

* Tailwind CSS (CDN)
* Vanilla JavaScript (Fetch API)

---

## Step 10: Run the Application

Start Laravel server:

```bash
php artisan serve
```

Start queue worker in separate terminal:

```bash
php artisan queue:work
```

---

## Step 11: Testing the Application

1. Open browser at:
   [http://localhost:8000](http://localhost:8000)
   <img width="1442" height="930" alt="image" src="https://github.com/user-attachments/assets/17707c74-fe4a-4ad2-b27a-e0f0e2bc05d5" />


3. Create test orders

4. Click:

   * Process Sync
   * Process Queued
   * Process Bulk Orders

5. Observe:

   * Queue worker processing jobs
   * Status changing from pending to processed
   * Notes updating

---

## Key Features Demonstrated

### QueueableAction Trait

Makes any class queueable without creating a Job class.

### onQueue() Method

Allows specifying queue execution.

### Synchronous vs Asynchronous Execution

Same action class works both ways.

### Action Chaining

Actions can call other actions.

### Failure Handling

failed() method handles exceptions and updates state.

### Parameter Passing

Actions can accept models and additional options.

---

## Project Structure

```
queueable-actions-demo/
├── app/
│   ├── Actions/
│   │   ├── ProcessOrderAction.php
│   │   └── ProcessBulkOrdersAction.php
│   ├── Http/
│   │   └── Controllers/
│   │       └── OrderController.php
│   └── Models/
│       └── Order.php
├── resources/
│   └── views/
│       └── orders.blade.php
├── routes/
│   └── web.php
└── database/
    └── migrations/
```

---

## Why Use Queueable Actions

Traditional Approach:

* Create Job class
* Dispatch job
* Maintain separate service logic

Queueable Actions Approach:

* Write clean service class
* Add QueueableAction trait
* Execute immediately or queue when needed

Benefits:

* Cleaner architecture
* Reusable business logic
* Less boilerplate
* Better separation of concerns
* Improved performance for heavy tasks

---

## Conclusion

This project demonstrates how Queueable Actions in Laravel simplify background processing while maintaining clean, testable service classes.

It is ideal for:

* Order processing systems
* Email dispatching
* Report generation
* Payment processing
* Bulk operations

You can extend this project by:

* Adding retry configuration
* Using Redis queue driver
* Adding job monitoring
* Implementing queue priorities
* Adding notifications after processing

Queueable Actions provide a modern, clean way to handle background jobs in Laravel applications.

