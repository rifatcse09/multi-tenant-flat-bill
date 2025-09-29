<?php

namespace App\Notifications;

use App\Models\Bill;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BillCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Bill $bill
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
        $amountFormatted = number_format($this->bill->amount, 2);

        return (new MailMessage)
            ->subject("New Bill Created - {$monthFormatted}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new bill has been created for you.")
            ->line("**Bill Details:**")
            ->line("Month: {$monthFormatted}")
            ->line("Category: {$this->bill->category->name}")
            ->line("Amount: $" . $amountFormatted)
            ->line("Flat: {$this->bill->flat->building->name} - Flat {$this->bill->flat->flat_number}")
            ->when($this->bill->due_carry_forward > 0, function ($message) {
                return $message->line("Previous Outstanding: $" . number_format($this->bill->due_carry_forward, 2));
            })
            ->when($this->bill->notes, function ($message) {
                return $message->line("Notes: {$this->bill->notes}");
            })
            ->line("Please make the payment at your earliest convenience.")
            ->action('View Bill Details', url('/owner/bills'))
            ->line('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'bill_id' => $this->bill->id,
            'amount' => $this->bill->amount,
            'month' => $this->bill->month->format('Y-m-d'),
            'category' => $this->bill->category->name,
        ];
    }
}