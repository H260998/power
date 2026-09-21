<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Defaults carried over verbatim (translated) from the design prototype's copy.
     */
    private function defaults(): array
    {
        return [
            'store_name' => 'ALO E-SHOP',
            'store_email' => 'contact@power-store.tn',
            'store_phone' => '+216 20 000 000',
            'shipping_fee' => '7.990',
            'free_shipping_threshold' => '150.000',

            'hero_title' => ['fr' => 'Définissez Votre', 'en' => 'Define Your', 'ar' => 'اكتشف'],
            'hero_highlight' => ['fr' => 'Juste Pour Vous', 'en' => 'Just For You', 'ar' => 'قوتك من أجلك'],
            'hero_strength_word' => ['fr' => 'FORCE', 'en' => 'STRENGTH', 'ar' => 'قوتك'],
            'hero_subtitle' => [
                'fr' => 'Sublimez votre style au quotidien avec des pièces conçues pour la confiance, le confort et un style sans compromis.',
                'en' => 'Elevate your everyday look with pieces built for confidence, comfort and unapologetic style.',
                'ar' => 'ارتقِ بإطلالتك اليومية بقطع مصممة للثقة والراحة وأسلوب لا يساوم.',
            ],

            'about_title' => ['fr' => 'Notre Histoire', 'en' => 'Our Story', 'ar' => 'قصتنا'],
            'about_text' => [
                'fr' => "ALO E-SHOP est plus qu'une boutique — c'est un univers de style. Nous sélectionnons des pièces pour celles et ceux qui avancent avec confiance et affirment leur personnalité.",
                'en' => 'ALO E-SHOP is more than a store — it is a world of style. We select pieces for people who move with confidence and express their personality.',
                'ar' => 'ALO E-SHOP أكثر من مجرد متجر — إنه عالم من الأناقة. نختار قطعًا لمن يتقدمون بثقة ويعبّرون عن شخصيتهم.',
            ],

            'feature1_title' => ['fr' => 'Qualité Premium', 'en' => 'Premium Quality', 'ar' => 'جودة فاخرة'],
            'feature1_text' => ['fr' => 'Les meilleurs matériaux pour un confort maximal.', 'en' => 'Finest materials for maximum comfort.', 'ar' => 'أفضل الخامات لأقصى درجات الراحة.'],
            'feature2_title' => ['fr' => 'Livraison Rapide', 'en' => 'Fast Delivery', 'ar' => 'توصيل سريع'],
            'feature2_text' => ['fr' => 'Rapide et fiable, partout dans le pays.', 'en' => 'Quick and reliable, nationwide.', 'ar' => 'سريع وموثوق في جميع أنحاء البلاد.'],
            'feature3_title' => ['fr' => 'Conçu Pour Durer', 'en' => 'Built To Last', 'ar' => 'مصمم ليدوم'],
            'feature3_text' => ['fr' => 'Des tissus durables qui tiennent la distance.', 'en' => 'Durable fabrics that go the distance.', 'ar' => 'أقمشة متينة تدوم طويلاً.'],

            'newsletter_title' => ['fr' => 'Rejoignez Le Club ALO', 'en' => 'Join The ALO Club', 'ar' => 'انضم إلى نادي ALO'],
            'newsletter_text' => [
                'fr' => 'Soyez informé en premier des nouveautés et offres exclusives.',
                'en' => 'Be first to know about new drops and exclusive offers.',
                'ar' => 'كن أول من يعلم بالمنتجات الجديدة والعروض الحصرية.',
            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->defaults() as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
