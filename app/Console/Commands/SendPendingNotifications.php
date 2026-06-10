<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendPendingNotifications extends Command
{
    protected $signature = 'notifications:send';
    protected $description = 'Send pending undelivered notifications';

    public function handle(): void
    {
        if (!class_exists(Notification::class)) {
            $this->warn('Notification model not found. Skipping.');
            return;
        }

        $pending = Notification::where('is_sent', false)
            ->where('scheduled_at', '<=', now())
            ->take(50)
            ->get();

        if ($pending->isEmpty()) {
            return;
        }

        $sent = 0;
        $failed = 0;

        foreach ($pending as $notification) {
            try {
                if ($notification->type === 'email' && $notification->user) {
                    \Illuminate\Support\Facades\Mail::raw(
                        $notification->message,
                        function ($message) use ($notification) {
                            $message->to($notification->user->email)
                                ->subject($notification->title ?? 'Notification');
                        }
                    );
                }

                $notification->is_sent = true;
                $notification->sent_at = now();
                $notification->save();
                $sent++;
            } catch (\Exception $e) {
                Log::error("Failed to send notification #{$notification->id}: {$e->getMessage()}");
                $failed++;
            }
        }

        if ($sent > 0 || $failed > 0) {
            $this->line("Notifications: {$sent} sent, {$failed} failed.");
        }
    }
}
