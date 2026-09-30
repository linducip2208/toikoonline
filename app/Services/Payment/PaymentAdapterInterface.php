<?php

namespace App\Services\Payment;

/**
 * Payment gateway adapter contract.
 *
 * Payload conventions (all adapters):
 * - createTransaction(['order_id'|'order_code', 'amount'|'gross_amount' (int IDR minor),
 *   'customer'?, 'items'?, 'callback_url'?, 'return_url'?])
 *   => ['success' bool, 'redirect_url'?|..., 'token'?|..., 'transaction_id'?...,
 *       'message'?, 'raw'?]
 * - verifyCallback($requestData incl. '_headers' when signature is header-based): bool
 * - getTransactionStatus($transactionId): ['success' bool, 'data'?|..., 'message'?]
 *
 * Money is integer IDR. Never log secrets (keys, signatures, full payloads).
 */
interface PaymentAdapterInterface
{
    public function createTransaction(array $payload): array;
    public function getTransactionStatus(string $transactionId): array;
    public function verifyCallback(array $requestData): bool;
    public function getChannels(): array;
}
