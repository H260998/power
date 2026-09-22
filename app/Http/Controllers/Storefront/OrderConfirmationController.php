<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderConfirmationController extends Controller
{
    public function show(Request $request, string $orderNumber): View
    {
        abort_unless($request->session()->has('confirmed_orders.'.$orderNumber), 404);

        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        $purchasePayload = [
            'content_ids' => $order->items->pluck('product_id')->filter()->values()->all(),
            'value' => (float) $order->total,
            'currency' => 'TND',
        ];

        return view('storefront.confirmation', [
            'order' => $order,
            'purchaseEventId' => $order->meta['purchase_event_id'] ?? null,
            'purchasePayload' => $purchasePayload,
        ]);
    }
}
