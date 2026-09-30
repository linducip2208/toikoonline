<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Transactional order email. Body comes from the matching EmailTemplate row
 * ({{var}} placeholders) when present, else a plain essential fallback.
 * No marketing copy is hardcoded.
 */
class OrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $kind;

    public function __construct(public Order $order, string $kind = 'order.paid')
    {
        $this->kind = $kind;
    }

    protected function variables(): array
    {
        $store = (string) (\App\Models\BusinessSetting::getValue('site_name', config('app.name')) ?? config('app.name'));

        return [
            'customer.name' => (string) ($this->order->user?->name ?? ''),
            'order.number' => (string) $this->order->code,
            'order.code' => (string) $this->order->code,
            'order.total' => 'Rp' . number_format((float) $this->order->grand_total, 0, ',', '.'),
            'store.name' => $store,
        ];
    }

    protected function renderBody(string $template): string
    {
        foreach ($this->variables() as $key => $value) {
            $template = str_replace(['{{' . $key . '}}', '{{ ' . $key . ' }}'], $value, $template);
        }

        return $template;
    }

    public function build(): static
    {
        $identifier = match ($this->kind) {
            'order.shipped' => 'order_shipped',
            'order.delivered' => 'order_delivered',
            'order.created', 'payment.failed' => 'order_paid',
            default => 'order_paid',
        };

        $tpl = EmailTemplate::where('identifier', $identifier)->where('status', true)->first();

        $subject = $tpl?->subject ?: ('Order ' . $this->order->code . ' — ' . $this->kind);
        $html = $tpl?->default_text
            ? $this->renderBody($tpl->default_text)
            : $this->renderBody('Hi {{customer.name}}, your order {{order.number}} ({{order.total}}) at {{store.name}} is now: ' . $this->kind . '.');

        return $this->subject($subject)->html($html);
    }
}
