<?php

namespace App\Services\B2B;

use App\Models\Coupon;
use App\Models\Quote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class QuoteService
{
    public function create(array $data): Quote
    {
        $data = validator($data, [
            'company_id' => 'required|exists:companies,id',
            'user_id' => 'required|exists:users,id',
            'valid_until' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant' => 'nullable|string|max:255',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|integer|min:0',
        ])->validate();

        return DB::transaction(function () use ($data) {
            $quote = Quote::create([
                'company_id' => $data['company_id'],
                'user_id' => $data['user_id'],
                'status' => 'draft',
                'valid_until' => $data['valid_until'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $row) {
                $quote->items()->create([
                    'product_id' => $row['product_id'],
                    'variant' => $row['variant'] ?? null,
                    'qty' => (int) $row['qty'],
                    'price' => (int) $row['price'],
                ]);
            }

            return $quote->fresh('items');
        });
    }

    public function send(Quote $quote): Quote
    {
        if ($quote->status !== 'draft') {
            throw ValidationException::withMessages(['status' => 'Hanya draf yang bisa dikirim.']);
        }
        $quote->update(['status' => 'sent']);

        return $quote->fresh();
    }

    /**
     * Approve a sent quote. Generates a single-use fixed/percent coupon
     * bound to the quoting user, valid until quote valid_until.
     *
     * @param array{discount:int,discount_type:'fixed'|'percent'} $terms negotiated terms
     */
    public function approve(Quote $quote, array $terms = []): Quote
    {
        return DB::transaction(function () use ($quote, $terms) {
            $quote->loadMissing('items');

            if (! in_array($quote->status, ['draft', 'sent'], true)) {
                throw ValidationException::withMessages(['status' => 'Hanya draf/terkirim yang bisa disetujui.']);
            }
            if ($quote->valid_until && $quote->valid_until->isPast()) {
                $quote->update(['status' => 'expired']);
                throw ValidationException::withMessages(['status' => 'Penawaran sudah kedaluwarsa.']);
            }
            if ($quote->coupon_id) {
                throw ValidationException::withMessages(['status' => 'Penawaran ini sudah punya kupon.']);
            }

            $terms = validator($terms, [
                'discount' => 'nullable|numeric|min:0',
                'discount_type' => 'nullable|in:fixed,percent',
            ])->validate();

            $total = (int) $quote->items->sum(fn ($i) => (int) $i->price * (int) $i->qty);
            $discount = (int) round((float) ($terms['discount'] ?? 0));
            $discountType = $terms['discount_type'] ?? 'fixed';
            if ($discountType === 'percent') {
                $discount = min(100, $discount);
            } else {
                $discount = min($discount, $total);
            }

            $coupon = Coupon::create([
                'user_id' => $quote->user_id,
                'type' => 'standard',
                'code' => 'QT-'.strtoupper(Str::random(8)),
                'details' => json_encode(['quote_id' => $quote->id, 'company_id' => $quote->company_id]),
                'discount' => $discount,
                'discount_type' => $discountType === 'percent' ? 'percent' : 'amount',
                'start_date' => now()->timestamp,
                'end_date' => $quote->valid_until ? $quote->valid_until->timestamp : now()->addDays(30)->timestamp,
                'min_buy' => 0,
                'max_discount' => $discountType === 'percent' ? $total : 0,
                'status' => true,
            ]);

            $quote->update(['status' => 'approved', 'coupon_id' => $coupon->id]);

            return $quote->fresh(['items', 'coupon']);
        });
    }

    public function reject(Quote $quote): Quote
    {
        if (! in_array($quote->status, ['draft', 'sent'], true)) {
            throw ValidationException::withMessages(['status' => 'Hanya draf/terkirim yang bisa ditolak.']);
        }
        $quote->update(['status' => 'rejected']);

        return $quote->fresh();
    }
}
