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
            'order_number' => ['required', 'string', 'regex:/\APWR-[0-9]{6}\z/'],
            'phone' => ['required', 'string', 'regex:/\A[234579][0-9]{7}\z/'],
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
