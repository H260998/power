<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrackingEvent;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingController extends Controller
{
    public function edit(SettingsService $settings): View
    {
        return view('admin.marketing.edit', [
            'metaPixelId' => $settings->get('meta_pixel_id'),
            'conversionApiToken' => $settings->get('conversion_api_token'),
            'recentEvents' => TrackingEvent::latest()->take(20)->get(),
            'eventCounts' => TrackingEvent::selectRaw('event_name, count(*) as total')
                ->groupBy('event_name')
                ->pluck('total', 'event_name'),
        ]);
    }

    public function update(Request $request, SettingsService $settings): RedirectResponse
    {
        $request->validate([
            'meta_pixel_id' => ['nullable', 'string', 'max:50'],
            'conversion_api_token' => ['nullable', 'string', 'max:500'],
        ]);

        $settings->setMany([
            'meta_pixel_id' => $request->input('meta_pixel_id'),
            'conversion_api_token' => $request->input('conversion_api_token'),
        ]);

        return back()->with('status', __('admin.marketing_updated'));
    }
}
