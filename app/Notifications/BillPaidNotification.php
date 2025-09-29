<?php

namespace App\Notifications;

use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BillPaidNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Bill $bill,
        public Payment $payment
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $monthFormatted = $this->bill->month->format('F Y');
        $paymentAmount = number_format($this->payment->amount, 2);
        $totalDue = $this->bill->amount + $this->bill->due_carry_forward;
        $totalPaid = $this->bill->payments()->sum('amount');
        $remainingAmount = max(0, $totalDue - $totalPaid);

        $isFullyPaid = $remainingAmount <= 0;

        return (new MailMessage)
            ->subject($isFullyPaid ? "Bill Fully Paid - {$monthFormatted}" : "Payment Received - {$monthFormatted}")
            ->greeting("Hello {$notifiable->name},")
            ->line($isFullyPaid ?
                "Great news! A bill has been fully paid." :
                "A payment has been received for one of your bills.")
            ->line("**Payment Details:**")
            ->line("Amount Paid: $" . $paymentAmount)
            ->line("Payment Date: " . $this->payment->paid_at->format('M d, Y'))
            ->line("Payment Method: " . ucfirst($this->payment->payment_method ?? 'Cash'))
            ->line("")
            ->line("**Bill Details:**")
            ->line("Month: {$monthFormatted}")
            ->line("Category: {$this->bill->category->name}")
            ->line("Flat: {$this->bill->flat->building->name} - Flat {$this->bill->flat->flat_number}")
            ->line("Tenant: " . ($this->bill->tenant->name ?? 'Not assigned'))
            ->line("Bill Amount: $" . number_format($this->bill->amount, 2))
            ->when($this->bill->due_carry_forward > 0, function ($message) {
                return $message->line("Previous Outstanding: $" . number_format($this->bill->due_carry_forward, 2));
            })
            ->line("Total Due: $" . number_format($totalDue, 2))
            ->line("Total Paid: $" . number_format($totalPaid, 2))
            ->when(!$isFullyPaid, function ($message) use ($remainingAmount) {
                return $message->line("Remaining: $" . number_format($remainingAmount, 2))
                              ->line("Status: Partially Paid");
            })
            ->when($isFullyPaid, function ($message) {
                return $message->line("Status: Fully Paid ✓");
            })
            ->when($this->payment->notes, function ($message) {
                return $message->line("Payment Notes: {$this->payment->notes}");
            })
            ->action('View Payment Details', url('/owner/bills'))
            ->line('Thank you for your business!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'bill_id' => $this->bill->id,
            'payment_id' => $this->payment->id,
            'payment_amount' => $this->payment->amount,
            'bill_status' => $this->bill->status,
            'month' => $this->bill->month->format('Y-m-d'),
        ];
    }
}