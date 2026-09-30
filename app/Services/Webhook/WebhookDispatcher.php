<?php

namespace App\Services\Webhook;

use App\Models\WebhookDelivery;
use App\Models\WebhookSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Outbound webhooks with HMAC-SHA256 signing.
 * Dispatch is sync-safe: rows are created inside afterCommit so queued
 * jobs are never dispatched for rolled-back transactions.
 */
class WebhookDispatcher
{
    public static function supportedEvents(): array
    {
        return ['order.paid', 'order.shipped', 'order.delivered'];
    }

    public static function sign(string $secret, string $payloadJson): string
    {
        return hash_hmac('sha256', $payloadJson, $secret);
    }

    public static function dispatch(string $event, array $payload): void
    {
        if (!in_array($event, self::supportedEvents(), true)) {
            return;
        }

        $subscriptions = WebhookSubscription::where('event', $event)
            ->where('is_active', true)
            ->get();

        foreach ($subscriptions as $sub) {
            DB::afterCommit(function () use ($sub, $event, $payload) {
                $delivery = WebhookDelivery::create([
                    'subscription_id' => $sub->id,
                    'event' => $event,
                    'payload' => $payload,
                    'status' => 'pending',
                    'attempts' => 0,
                ]);

                static::attempt($delivery->fresh());
            });
        }
    }

    public static function attempt(WebhookDelivery $delivery, int $timeoutSeconds = 15): bool
    {
        $sub = $delivery->subscription;
        if (!$sub || !$sub->is_active) {
            $delivery->update(['status' => 'skipped']);
            return false;
        }

        $payloadJson = json_encode($delivery->payload ?? []);
        $signature = self::sign($sub->secret, $payloadJson);

        $delivery->increment('attempts');

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Webhook-Event' => $delivery->event,
                'X-Webhook-Signature' => 'sha256=' . $signature,
            ])->timeout($timeoutSeconds)->post($sub->url, $delivery->payload ?? []);

            $ok = $response->successful();
            $delivery->update([
                'status' => $ok ? 'delivered' : 'failed',
                'response' => substr($response->status() . ' ' . $response->body(), 0, 2000),
                'next_retry_at' => $ok ? null : now()->addMinutes(self::backoffMinutes($delivery->attempts)),
            ]);

            return $ok;
        } catch (\Exception $e) {
            Log::warning('Webhook delivery failed', ['delivery' => $delivery->id, 'error' => $e->getMessage()]);
            $delivery->update([
                'status' => 'failed',
                'response' => substr('exception: ' . $e->getMessage(), 0, 2000),
                'next_retry_at' => now()->addMinutes(self::backoffMinutes($delivery->attempts)),
            ]);

            return false;
        }
    }

    public static function backoffMinutes(int $attempts): int
    {
        return match (true) {
            $attempts <= 1 => 5,
            $attempts === 2 => 30,
            $attempts === 3 => 120,
            default => 720,
        };
    }
}
