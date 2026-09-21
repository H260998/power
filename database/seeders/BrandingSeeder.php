<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class BrandingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'store_name' => 'ALO E-SHOP',
            'about_text' => [
                'fr' => "ALO E-SHOP est plus qu'une boutique — c'est un univers de style. Nous sélectionnons des pièces pour celles et ceux qui avancent avec confiance et affirment leur personnalité.",
                'en' => 'ALO E-SHOP is more than a store — it is a world of style. We select pieces for people who move with confidence and express their personality.',
                'ar' => 'ALO E-SHOP أكثر من مجرد متجر — إنه عالم من الأناقة. نختار قطعًا لمن يتقدمون بثقة ويعبّرون عن شخصيتهم.',
            ],
            'newsletter_title' => [
                'fr' => 'Rejoignez Le Club ALO',
                'en' => 'Join The ALO Club',
                'ar' => 'انضم إلى نادي ALO',
            ],
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget('settings.all');
    }
}
