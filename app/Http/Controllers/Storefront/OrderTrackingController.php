<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    public function index(): View
    {
        return view('storefront.order-tracking', ['order' => null]);
    }

    public function lookup(Request $request): View
    {
        $request->validate([
            'order_number' => ['required', 'string'],
            'phone' => ['required', 'string'],
        ]);

        $order = Order::with('items')
            ->where('order_number', $request->string('order_number'))
            ->where('phone', $request->string('phone'))
            ->first();

        return view('storefront.order-tracking', [
            'order' => $order,
            'notFound' => ! $order,
        ]);
    }
}
