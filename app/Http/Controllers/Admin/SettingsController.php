<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public const SCALAR_KEYS = ['store_name', 'store_email', 'store_phone', 'shipping_fee', 'free_shipping_threshold'];

    public const TRANSLATABLE_KEYS = [
        'hero_title', 'hero_highlight', 'hero_strength_word', 'hero_subtitle',
        'about_title', 'about_text',
        'feature1_title', 'feature1_text', 'feature2_title', 'feature2_text', 'feature3_title', 'feature3_text',
        'newsletter_title', 'newsletter_text',
    ];

    public function __construct(private readonly SettingsService $settings) {}

    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => $this->settings->all(),
            'scalarKeys' => self::SCALAR_KEYS,
            'translatableKeys' => self::TRANSLATABLE_KEYS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'store_email' => ['nullable', 'email'],
            'shipping_fee' => ['nullable', 'numeric', 'min:0'],
            'free_shipping_threshold' => ['nullable', 'numeric', 'min:0'],
        ]);

        $values = [];

        foreach (self::SCALAR_KEYS as $key) {
            $values[$key] = $request->input($key);
        }

        foreach (self::TRANSLATABLE_KEYS as $key) {
            $values[$key] = [
                'fr' => $request->input("{$key}.fr"),
                'en' => $request->input("{$key}.en"),
                'ar' => $request->input("{$key}.ar"),
            ];
        }

        $this->settings->setMany($values);

        return back()->with('status', __('admin.settings_updated'));
    }
}
