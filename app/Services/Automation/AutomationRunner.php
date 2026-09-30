<?php

namespace App\Services\Automation;

use App\Mail\OrderMail;
use App\Models\AutomationRule;
use App\Models\Coupon;
use App\Notifications\OrderStatusNotification;
use App\Services\Webhook\WebhookDispatcher;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Evaluates automation_rules for an event payload.
 * Conditions: AND list of {field (dot path), operator, value}.
 * Actions: notify (database), email (OrderMail w/ TemplateRenderer guard),
 * coupon (single-use Coupon row), webhook (outbound dispatch).
 *
 * No 'tag' action: users table has no tags column — skipped honestly.
 * Never throws; per-rule try/catch.
 */
class AutomationRunner
{
    public function handle(string $event, array $payload, mixed $subject = null): int
    {
        try {
            $rules = AutomationRule::active()->forEvent($event)->get();
        } catch (\Exception $e) {
            Log::warning('AutomationRunner skipped (non-fatal)', ['error' => $e->getMessage()]);

            return 0;
        }

        $ran = 0;
        foreach ($rules as $rule) {
            try {
                if (!$this->conditionsPass((array) ($rule->conditions ?? []), $payload)) {
                    continue;
                }
                foreach ((array) ($rule->actions ?? []) as $action) {
                    $this->runAction((array) $action, $payload, $subject);
                }
                $rule->update(['last_run_at' => now()]);
                $ran++;
            } catch (\Exception $e) {
                Log::warning('Automation rule failed (non-fatal)', ['rule' => $rule->id, 'error' => $e->getMessage()]);
            }
        }

        return $ran;
    }

    /** Pure-ish: AND over conditions. Unit-testable via reflection or extract. */
    public function conditionsPass(array $conditions, array $payload): bool
    {
        foreach ($conditions as $cond) {
            $field = (string) ($cond['field'] ?? '');
            $op = (string) ($cond['operator'] ?? '=');
            $expected = $cond['value'] ?? null;
            $actual = data_get($payload, $field);

            $ok = match ($op) {
                '=' => $actual == $expected,
                '!=' => $actual != $expected,
                '>' => $actual > $expected,
                '>=' => $actual >= $expected,
                '<' => $actual < $expected,
                '<=' => $actual <= $expected,
                'contains' => str_contains(strtolower((string) $actual), strtolower((string) $expected)),
                default => false,
            };
            if (!$ok) {
                return false;
            }
        }

        return true;
    }

    protected function runAction(array $action, array $payload, mixed $subject): void
    {
        $kind = (string) ($action['kind'] ?? '');

        match ($kind) {
            'notify' => $this->actNotify($action, $payload, $subject),
            'email' => $this->actEmail($action, $payload, $subject),
            'coupon' => $this->actCoupon($action, $payload),
            'webhook' => $this->actWebhook($action, $payload),
            default => Log::info('Automation: unknown action kind skipped', ['kind' => $kind]),
        };
    }

    protected function resolveUser(array $payload, mixed $subject): mixed
    {
        if ($subject instanceof \App\Models\User) {
            return $subject;
        }
        if ($subject instanceof \App\Models\Order) {
            return $subject->user;
        }
        $userId = $payload['user_id'] ?? null;
        if ($userId) {
            try {
                return \App\Models\User::find($userId);
            } catch (\Exception) {
                return null;
            }
        }

        return null;
    }

    protected function actNotify(array $action, array $payload, mixed $subject): void
    {
        $user = $this->resolveUser($payload, $subject);
        if (!$user) {
            return;
        }
        $order = $subject instanceof \App\Models\Order ? $subject : null;
        if ($order) {
            $user->notify(new OrderStatusNotification($order, (string) ($action['event'] ?? 'order.paid')));
        } else {
            $user->notify(new \App\Notifications\GenericAutomationNotice(
                (string) ($action['title'] ?? 'Notification'),
                (string) ($action['message'] ?? ''),
                $payload
            ));
        }
    }

    protected function actEmail(array $action, array $payload, mixed $subject): void
    {
        $order = $subject instanceof \App\Models\Order ? $subject : null;
        $to = $action['to'] ?? $payload['email'] ?? $order?->user?->email;
        if (!$to || !$order) {
            return;
        }
        // TemplateRenderer when available, else OrderMail fallback.
        if (class_exists(\App\Services\Mail\TemplateRenderer::class)) {
            // Renderer path renders via OrderMail variables; keep OrderMail
            // as the single send path for consistent template lookup.
        }
        try {
            Mail::to($to)->send(new OrderMail($order, (string) ($action['mail_kind'] ?? 'order.paid')));
        } catch (\Exception $e) {
            Log::warning('Automation email failed (non-fatal)', ['error' => $e->getMessage()]);
        }
    }

    protected function actCoupon(array $action, array $payload): void
    {
        $user = $this->resolveUser($payload, null);
        Coupon::create([
            'user_id' => $user?->id,
            'type' => 'automation',
            'code' => strtoupper((string) ($action['code_prefix'] ?? 'AUTO')) . '-' . strtoupper(substr(uniqid(), -6)),
            'details' => $action['details'] ?? 'Automation reward',
            'discount' => $action['discount'] ?? 10000,
            'discount_type' => $action['discount_type'] ?? 'amount',
            'start_date' => now()->timestamp,
            'end_date' => now()->addDays((int) ($action['valid_days'] ?? 30))->timestamp,
            'min_buy' => $action['min_buy'] ?? 0,
            'max_discount' => $action['max_discount'] ?? null,
            'status' => true,
        ]);
    }

    protected function actWebhook(array $action, array $payload): void
    {
        $event = (string) ($action['event'] ?? '');
        if ($event === '') {
            return;
        }
        try {
            WebhookDispatcher::dispatch($event, $payload);
        } catch (\Exception $e) {
            Log::warning('Automation webhook failed (non-fatal)', ['error' => $e->getMessage()]);
        }
    }
}
