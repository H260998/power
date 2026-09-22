<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\View\View;

class InformationController extends Controller
{
    private const PAGES = ['help', 'about', 'contact', 'privacy', 'terms'];

    public function show(string $page, SettingsService $settings): View
    {
        abort_unless(in_array($page, self::PAGES, true), 404);

        $content = trans('pages.'.$page);
        abort_unless(is_array($content), 404);

        return view('storefront.information', [
            'title' => $content['title'],
            'pageKey' => $page,
            'content' => $content,
            'storeEmail' => $settings->get('store_email'),
            'storePhone' => $settings->get('store_phone'),
        ]);
    }
}
