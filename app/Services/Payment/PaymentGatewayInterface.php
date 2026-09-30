<?php

namespace App\Services\Payment;

/**
 * Formalized gateway contract. Extends the existing adapter interface
 * (backward compatible — existing adapters keep implementing
 * PaymentAdapterInterface) with fee + customer-instruction support.
 *
 * Gateway config json keys (PaymentGatewayConfig.config):
 * - payment_fee: int IDR flat fee added at checkout, or
 *   {percent: float, cap: int} for percent fee. Default 0.
 * - payment_instructions: string shown at checkout (transfer account
 *   number, COD note, etc.). Rendered by the checkout view (integrator).
 */
interface PaymentGatewayInterface extends PaymentAdapterInterface
{
    /** Flat/percent fee in IDR minor for the given order amount. */
    public function feeFor(int $amountMinor): int;

    /** Human instruction text for checkout display, or null. */
    public function instructions(): ?string;
}
