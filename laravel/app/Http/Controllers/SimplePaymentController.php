<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\SimplePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Simple Payment Controller
 *
 * Handles payment status updates for tickets.
 * Only admins can manually change payment statuses.
 * This is designed to be simple and beginner-friendly.
 */
class SimplePaymentController extends Controller
{
    protected $paymentService;

    public function __construct(SimplePaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Check if user is admin (simple helper method)
     */
    private function checkAdminAccess()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Only admins can manage payments');
        }
    }

    /**
     * Show payment management dashboard
     */
    public function index()
    {
        $this->checkAdminAccess();

        $stats = $this->paymentService->getPaymentStats();
        $pendingPayments = $this->paymentService->getPendingPayments();
        $failedPayments = $this->paymentService->getFailedPayments();

        return view('admin.payments.index', compact('stats', 'pendingPayments', 'failedPayments'));
    }

    /**
     * Mark a ticket as paid
     */
    public function markPaid(Request $request, Ticket $ticket)
    {
        $this->checkAdminAccess();

        $request->validate([
            'payment_amount' => 'nullable|numeric|min:0',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        $success = $this->paymentService->markAsPaid(
            $ticket,
            $request->payment_amount,
            $request->payment_reference
        );

        if ($success) {
            return redirect()->back()->with('success', 'Ticket marked as paid successfully!');
        }

        return redirect()->back()->with('error', 'Cannot mark this ticket as paid. It may not be pending.');
    }

    /**
     * Mark a payment as failed
     */
    public function markFailed(Request $request, Ticket $ticket)
    {
        $this->checkAdminAccess();

        $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $success = $this->paymentService->markAsFailed($ticket, $request->reason);

        if ($success) {
            return redirect()->back()->with('success', 'Payment marked as failed.');
        }

        return redirect()->back()->with('error', 'Cannot mark this payment as failed. It may not be pending.');
    }

    /**
     * Refund a ticket
     */
    public function refund(Request $request, Ticket $ticket)
    {
        $this->checkAdminAccess();

        $request->validate([
            'refund_reference' => 'nullable|string|max:255',
        ]);

        $success = $this->paymentService->refundTicket($ticket, $request->refund_reference);

        if ($success) {
            return redirect()->back()->with('success', 'Ticket refunded successfully!');
        }

        return redirect()->back()->with('error', 'Cannot refund this ticket. It may not be paid.');
    }

    /**
     * Retry a failed payment
     */
    public function retry(Ticket $ticket)
    {
        $this->checkAdminAccess();

        $success = $this->paymentService->retryPayment($ticket);

        if ($success) {
            return redirect()->back()->with('success', 'Payment status reset to pending. Customer can try again.');
        }

        return redirect()->back()->with('error', 'Cannot retry this payment. It may not have failed.');
    }
}
