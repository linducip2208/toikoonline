<?php

namespace App\Services\Payment;

/**
 * Pure fee/instruction helpers shared by all adapters.
 * Reads PaymentGatewayConfig.config keys: payment_fee, payment_instructions.
 * Money integer IDR. No I/O, Http::fake-friendly, unit-testable.
 */
class PaymentFees
{
    /**
     * @param array|string|int|float|null $feeCfg int flat, or ['flat'=>int] / ['percent'=>float,'cap'=>int]
     */
    public static function feeForConfig(mixed $feeCfg, int $amountMinor): int
    {
        if (is_array($feeCfg)) {
            if (isset($feeCfg['flat'])) {
                return max(0, (int) $feeCfg['flat']);
            }
            $percent = (float) ($feeCfg['percent'] ?? 0);
            if ($percent > 0) {
                $fee = (int) round($amountMinor * $percent / 100);
                if (isset($feeCfg['cap'])) {
                    $fee = min($fee, (int) $feeCfg['cap']);
                }

                return max(0, $fee);
            }

            return 0;
        }

        return max(0, (int) $feeCfg);
    }

    public static function instructionsForConfig(mixed $cfg): ?string
    {
        $text = is_array($cfg) ? ($cfg['payment_instructions'] ?? null) : null;

        return $text ? (string) $text : null;
    }
}
