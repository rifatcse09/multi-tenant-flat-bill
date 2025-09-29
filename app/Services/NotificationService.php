<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\BillCreatedNotification;
use App\Notifications\BillPaidNotification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send bill created notifications to relevant stakeholders.
     */
    public function sendBillCreatedNotifications(Bill $bill): void
    {
        try {
            // Load necessary relationships
            $bill->load(['tenant', 'flat.building', 'category', 'owner']);

            // Notify the tenant if bill is for tenant and tenant has email
            if ($bill->tenant && !empty($bill->tenant->email)) {
                Log::info('Sending bill created notification to tenant', [
                    'tenant_id' => $bill->tenant->id,
                    'tenant_email' => $bill->tenant->email,
                    'bill_id' => $bill->id
                ]);
                $this->sendNotificationSafely($bill->tenant, new BillCreatedNotification($bill));
            }

            // Always notify the owner
            if ($bill->owner && !empty($bill->owner->email)) {
                Log::info('Sending bill created notification to owner', [
                    'owner_id' => $bill->owner->id,
                    'owner_email' => $bill->owner->email,
                    'bill_id' => $bill->id
                ]);
                $this->sendNotificationSafely($bill->owner, new BillCreatedNotification($bill));
            }

            // Notify admins if configured
            $this->notifyAdmins(new BillCreatedNotification($bill));

        } catch (\Exception $e) {
            Log::error('Failed to send bill created notifications', [
                'bill_id' => $bill->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Send bill paid notifications to relevant stakeholders.
     */
    public function sendBillPaidNotifications(Bill $bill, Payment $payment): void
    {
        try {
            // Load necessary relationships
            $bill->load(['tenant', 'flat.building', 'category', 'owner']);

            // Always notify the owner about payment received
            if ($bill->owner && !empty($bill->owner->email)) {
                Log::info('Sending payment notification to owner', [
                    'owner_id' => $bill->owner->id,
                    'owner_email' => $bill->owner->email,
                    'bill_id' => $bill->id,
                    'payment_id' => $payment->id
                ]);
                $this->sendNotificationSafely($bill->owner, new BillPaidNotification($bill, $payment));
            }

            // Notify the tenant if they have email
            if ($bill->tenant && !empty($bill->tenant->email)) {
                Log::info('Sending payment notification to tenant', [
                    'tenant_id' => $bill->tenant->id,
                    'tenant_email' => $bill->tenant->email,
                    'bill_id' => $bill->id,
                    'payment_id' => $payment->id
                ]);
                $this->sendNotificationSafely($bill->tenant, new BillPaidNotification($bill, $payment));
            }

            // Notify admins if configured
            $this->notifyAdmins(new BillPaidNotification($bill, $payment));

        } catch (\Exception $e) {
            Log::error('Failed to send payment notifications', [
                'bill_id' => $bill->id,
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Send notification safely with error handling.
     */
    private function sendNotificationSafely($notifiable, $notification): void
    {
        try {
            // Check if the notifiable has the notify method
            if (!method_exists($notifiable, 'notify')) {
                Log::error('Notifiable object does not have notify method', [
                    'notifiable_type' => get_class($notifiable),
                    'notifiable_id' => $notifiable->id ?? 'unknown'
                ]);
                return;
            }

            // Check if notifiable has email
            $email = $notifiable->email ?? ($notifiable->routeNotificationForMail($notification) ?? null);
            if (empty($email)) {
                Log::warning('Notifiable does not have email address', [
                    'notifiable_type' => get_class($notifiable),
                    'notifiable_id' => $notifiable->id ?? 'unknown'
                ]);
                return;
            }

            $notifiable->notify($notification);

            Log::info('Notification sent successfully', [
                'notifiable_type' => get_class($notifiable),
                'notifiable_id' => $notifiable->id ?? 'unknown',
                'notification_type' => get_class($notification)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'notifiable_type' => get_class($notifiable),
                'notifiable_id' => $notifiable->id ?? 'unknown',
                'notification_type' => get_class($notification),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Notify all admin users.
     */
    private function notifyAdmins($notification): void
    {
        $admins = User::where('role', 'admin')
            ->whereNotNull('email')
            ->get();

        foreach ($admins as $admin) {
            $this->sendNotificationSafely($admin, $notification);
        }
    }

    /**
     * Send payment confirmation email to tenant.
     */
    public function sendPaymentConfirmation(Payment $payment): void
    {
        $bill = $payment->bill;

        if (!$bill) {
            return;
        }

        $this->sendBillPaidNotifications($bill, $payment);
    }

    /**
     * Send overdue bill notifications.
     */
    public function sendOverdueBillNotifications(): void
    {
        $overdueBills = Bill::where('status', '!=', 'paid')
            ->where('month', '<', now()->startOfMonth())
            ->with(['tenant', 'owner', 'flat.building', 'category'])
            ->get();

        foreach ($overdueBills as $bill) {
            // Send overdue notification to tenant
            if ($bill->tenant && $bill->tenant->email) {
                $this->sendNotificationSafely($bill->tenant, new BillCreatedNotification($bill));
            }

            // Notify owner about overdue bill
            if ($bill->owner && $bill->owner->email) {
                $this->sendNotificationSafely($bill->owner, new BillCreatedNotification($bill));
            }
        }
    }

    /**
     * Send bulk notifications for multiple bills.
     */
    public function sendBulkBillCreatedNotifications(array $bills): void
    {
        foreach ($bills as $bill) {
            $this->sendBillCreatedNotifications($bill);
        }
    }
}