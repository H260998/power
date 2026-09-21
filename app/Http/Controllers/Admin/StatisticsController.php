<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(): View
    {
        $since = Carbon::today()->subDays(13);

        $revenueByDay = Order::where('status', '!=', OrderStatus::Cancelled)
            ->where('created_at', '>=', $since)
            ->selectRaw('DATE(created_at) as day, SUM(total) as revenue')
            ->groupBy('day')
            ->pluck('revenue', 'day');

        $revenueSeries = collect(range(0, 13))->map(function ($i) use ($since, $revenueByDay) {
            $date = $since->copy()->addDays($i);
            $key = $date->format('Y-m-d');

            return [
                'label' => $date->format('d/m'),
                'value' => (float) ($revenueByDay[$key] ?? 0),
            ];
        });

        $topProducts = OrderItem::selectRaw('product_name, SUM(quantity) as units, SUM(line_total) as revenue')
            ->groupBy('product_name')
            ->orderByDesc('revenue')
            ->take(5)
            ->get();

        $categoryBreakdown = OrderItem::query()
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as category_name, SUM(order_items.line_total) as revenue')
            ->groupBy('categories.id')
            ->orderByDesc('revenue')
            ->get()
            ->map(fn ($row) => [
                'name' => json_decode($row->category_name, true)[app()->getLocale()] ?? $row->category_name,
                'revenue' => (float) $row->revenue,
            ]);

        return view('admin.statistics.index', [
            'revenueSeries' => $revenueSeries,
            'topProducts' => $topProducts,
            'categoryBreakdown' => $categoryBreakdown,
            'maxRevenue' => max($revenueSeries->max('value'), 1),
            'maxCategoryRevenue' => max($categoryBreakdown->max('revenue'), 1),
        ]);
    }
}
