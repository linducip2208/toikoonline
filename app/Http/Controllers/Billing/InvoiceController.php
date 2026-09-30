<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Billing\InvoiceService;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function invoice(Order $order, InvoiceService $service)
    {
        $this->authorize($order);

        return $service->invoicePdf($order)->download($service->invoiceFilename($order));
    }

    public function packingSlip(Order $order, InvoiceService $service)
    {
        $this->authorize($order);

        return $service->packingSlipPdf($order)->download($service->packingSlipFilename($order));
    }

    protected function authorize(Order $order): void
    {
        $user = Auth::user();

        if ($user && (method_exists($user, 'isAdmin') ? $user->isAdmin() : in_array($user->user_type ?? '', ['admin', 'staff'], true))) {
            return;
        }

        if (! $user || (int) $order->user_id !== (int) $user->id) {
            abort(403);
        }
    }
}
