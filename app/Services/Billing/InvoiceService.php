<?php

namespace App\Services\Billing;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    public function invoicePdf(Order $order): \Barryvdh\DomPDF\PDF
    {
        $order->loadMissing(['user', 'orderDetails.product']);

        return Pdf::loadView('billing.invoice', ['order' => $order])
            ->setPaper('a4', 'portrait');
    }

    public function packingSlipPdf(Order $order): \Barryvdh\DomPDF\PDF
    {
        $order->loadMissing(['user', 'orderDetails.product']);

        return Pdf::loadView('billing.packing-slip', ['order' => $order])
            ->setPaper('a4', 'portrait');
    }

    public function invoiceFilename(Order $order): string
    {
        return 'invoice-'.($order->code ?: $order->id).'.pdf';
    }

    public function packingSlipFilename(Order $order): string
    {
        return 'packing-slip-'.($order->code ?: $order->id).'.pdf';
    }
}
