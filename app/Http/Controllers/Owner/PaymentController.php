<?php

namespace App\Http\Controllers\Owner;

use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Http\Requests\Owner\StorePaymentRequest;

class PaymentController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function create(Request $request)
    {
        $ownerId = auth()->id();

        // Get bills for dropdown with calculated due amounts
        $bills = Bill::where('owner_id', $ownerId)
            ->where('status', '!=', 'paid')
            ->with(['flat.building', 'category', 'tenant', 'payments'])
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($bill) {
                $totalPaid = $bill->payments->sum('amount');
                $totalDue = $bill->amount + $bill->due_carry_forward;
                $bill->due = max(0, $totalDue - $totalPaid);
                return $bill;
            });

        // Handle selected bill (either from URL parameter or form selection)
        $selectedBillId = $request->get('bill_id');
        $bill = null; // Initialize as null for the view

        if ($selectedBillId) {
            $bill = $bills->firstWhere('id', $selectedBillId);
        }

        // If no bills available, show message but don't redirect
        if ($bills->isEmpty()) {
            session()->flash('info', 'No unpaid bills available for payment. Create a bill first.');
        }

        return view('owner.payments.create', compact('bills', 'bill'));
    }

    public function store(StorePaymentRequest $request)
    {
        try {
            $data = $request->validated();
            $ownerId = auth()->id();

            // Verify bill belongs to owner
            $bill = Bill::where('owner_id', $ownerId)
                ->findOrFail($data['bill_id']);

            // Create payment
            $payment = Payment::create([
                'bill_id' => $bill->id,
                'amount' => $data['amount'],
                'paid_at' => $data['paid_at'],
                'method' => $data['method'] ?? 'cash',
            ]);

            // Update bill status
            $this->updateBillStatus($bill);

            // Send notifications
            try {
                $this->notificationService->sendBillPaidNotifications($bill, $payment);
            } catch (\Exception $e) {
                // Log notification failure but don't break payment creation
                Log::error('Failed to send payment notifications', [
                    'payment_id' => $payment->id,
                    'bill_id' => $bill->id,
                    'error' => $e->getMessage()
                ]);
            }

            return redirect()->route('owner.bills.index')
                ->with('ok', 'Payment recorded successfully and notifications sent.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to record payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete a payment (hard delete since payments don't use soft deletes).
     */
    public function destroy(Payment $payment)
    {
        try {
            // Ensure owner can only delete payments for their own bills
            $bill = $payment->bill;
            abort_unless($bill->owner_id === auth()->id(), 403);

            // Hard delete the payment
            $payment->delete();

            // Update bill status after payment deletion
            $this->updateBillStatus($bill);

            return back()->with('ok', 'Payment deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete payment: ' . $e->getMessage());
        }
    }

    /**
     * Update bill status based on total payments.
     */
    private function updateBillStatus(Bill $bill): void
    {
        $totalPaid = $bill->payments()->sum('amount');
        $totalDue = $bill->amount + $bill->due_carry_forward;

        if ($totalPaid <= 0) {
            $status = 'unpaid';
        } elseif ($totalPaid >= $totalDue) {
            $status = 'paid';
        } else {
            $status = 'partial';
        }

        $bill->update(['status' => $status]);
    }
}