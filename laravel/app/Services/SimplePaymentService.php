<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

/**
 * Simple Payment Service
 *
 * This service handles payment status changes for tickets.
 * It's designed to be beginner-friendly and easy to understand.
 *
 * Uses the State Pattern: Each payment status represents a different state,
 * and we have clear methods to transition between states.
 */
class SimplePaymentService
{
    /**
     * Mark a ticket as paid
     * This changes the payment status from 'pending' to 'paid'
     */
    public function markAsPaid(Ticket $ticket, $paymentAmount = null, $paymentReference = null)
    {
        // Only allow payment if ticket is pending
        if (!$ticket->isPending()) {
            return false;
        }

        $ticket->update([
            'payment_status' => 'paid',
            'payment_amount' => $paymentAmount ?? $ticket->total_price,
            'paid_at' => now(),
            'payment_reference' => $paymentReference,
        ]);

        return true;
    }

    /**
     * Mark a ticket payment as failed
     */
    public function markAsFailed(Ticket $ticket, $reason = null)
    {
        // Only allow failure if ticket is pending
        if (!$ticket->isPending()) {
            return false;
        }

        $ticket->update([
            'payment_status' => 'failed',
            'payment_reference' => $reason,
        ]);

        return true;
    }

    /**
     * Refund a ticket
     * This changes the payment status from 'paid' to 'refunded'
     */
    public function refundTicket(Ticket $ticket, $refundReference = null)
    {
        // Only allow refund if ticket is paid
        if (!$ticket->isPaid()) {
            return false;
        }

        $ticket->update([
            'payment_status' => 'refunded',
            'payment_reference' => $refundReference ?? 'Refunded at ' . now(),
        ]);

        return true;
    }

    /**
     * Get payment statistics for admin dashboard
     */
    public function getPaymentStats()
    {
        return [
            'total_tickets' => Ticket::count(),
            'paid_tickets' => Ticket::where('payment_status', 'paid')->count(),
            'pending_payments' => Ticket::where('payment_status', 'pending')->count(),
            'failed_payments' => Ticket::where('payment_status', 'failed')->count(),
            'refunded_tickets' => Ticket::where('payment_status', 'refunded')->count(),
            'total_revenue' => Ticket::where('payment_status', 'paid')->sum('payment_amount'),
        ];
    }

    /**
     * Get all pending payments for admin review
     */
    public function getPendingPayments()
    {
        return Ticket::where('payment_status', 'pending')
            ->with(['user', 'event'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all failed payments for admin review
     */
    public function getFailedPayments()
    {
        return Ticket::where('payment_status', 'failed')
            ->with(['user', 'event'])
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Retry a failed payment (reset to pending)
     */
    public function retryPayment(Ticket $ticket)
    {
        // Only allow retry if payment failed
        if (!$ticket->isFailed()) {
            return false;
        }

        $ticket->update([
            'payment_status' => 'pending',
            'payment_reference' => null,
        ]);

        return true;
    }
}
