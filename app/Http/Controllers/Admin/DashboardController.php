<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\StockService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(StockService $stock): View
    {
        $orders = Order::where('status', '!=', OrderStatus::Cancelled);

        $revenue = (clone $orders)->sum('total');
        $orderCount = (clone $orders)->count();
        $customerCount = (clone $orders)->distinct('phone')->count('phone');
        $averageOrderValue = $orderCount > 0 ? $revenue / $orderCount : 0;

        return view('admin.dashboard', [
            'stats' => [
                ['label' => __('admin.stat_revenue'), 'value' => number_format($revenue, 3).' '.__('storefront.currency')],
                ['label' => __('admin.stat_orders'), 'value' => number_format($orderCount)],
                ['label' => __('admin.stat_customers'), 'value' => number_format($customerCount)],
                ['label' => __('admin.stat_aov'), 'value' => number_format($averageOrderValue, 3).' '.__('storefront.currency')],
            ],
            'recentOrders' => Order::latest()->take(6)->get(),
            'lowStockVariants' => $stock->lowStockVariants()->take(6),
        ]);
    }
}
