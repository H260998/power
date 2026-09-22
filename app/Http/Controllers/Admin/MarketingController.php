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
            'conversionApiConfigured' => filled($settings->get('conversion_api_token')),
            'recentEvents' => TrackingEvent::latest()->take(20)->get(),
            'eventCounts' => TrackingEvent::selectRaw('event_name, count(*) as total')
                ->groupBy('event_name')
                ->pluck('total', 'event_name'),
        ]);
    }

    public function update(Request $request, SettingsService $settings): RedirectResponse
    {
        $request->validate([
            'meta_pixel_id' => ['nullable', 'regex:/\A[0-9]{5,30}\z/'],
            'conversion_api_token' => ['nullable', 'string', 'max:500'],
            'clear_conversion_api_token' => ['nullable', 'boolean'],
        ]);

        $values = ['meta_pixel_id' => $request->input('meta_pixel_id')];

        if ($request->boolean('clear_conversion_api_token')) {
            $values['conversion_api_token'] = null;
        } elseif ($request->filled('conversion_api_token')) {
            $values['conversion_api_token'] = $request->input('conversion_api_token');
        }

        $settings->setMany($values);

        return back()->with('status', __('admin.marketing_updated'));
    }
}
