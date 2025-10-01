<?php

// Simple payment test script

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Payment Status Tracking Test ===\n\n";

// Check if we have any tickets
$ticketCount = App\Models\Ticket::count();
echo "Total tickets in database: {$ticketCount}\n";

if ($ticketCount > 0) {
    $ticket = App\Models\Ticket::first();
    echo "\nTesting with Ticket #{$ticket->id}:\n";
    echo "- Payment Status: {$ticket->payment_status}\n";
    echo "- isPaid(): " . ($ticket->isPaid() ? 'true' : 'false') . "\n";
    echo "- isPending(): " . ($ticket->isPending() ? 'true' : 'false') . "\n";
    echo "- isFailed(): " . ($ticket->isFailed() ? 'true' : 'false') . "\n";
    echo "- isRefunded(): " . ($ticket->isRefunded() ? 'true' : 'false') . "\n";

    // Test the service
    $paymentService = new App\Services\SimplePaymentService();
    $stats = $paymentService->getPaymentStats();

    echo "\n=== Payment Statistics ===\n";
    echo "Total tickets: {$stats['total_tickets']}\n";
    echo "Paid tickets: {$stats['paid_tickets']}\n";
    echo "Pending payments: {$stats['pending_payments']}\n";
    echo "Failed payments: {$stats['failed_payments']}\n";
    echo "Refunded tickets: {$stats['refunded_tickets']}\n";
    echo "Total revenue: \${$stats['total_revenue']}\n";
} else {
    echo "No tickets found in database.\n";
    echo "Create some test tickets first through the application.\n";
}

echo "\n=== Payment System Ready! ===\n";
echo "Admin can now manage payments at: /admin/payments\n";
