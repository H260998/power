<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * The full category tree from the project brief: [slug, [fr, en, ar], [children...]].
     */
    private function tree(): array
    {
        return [
            ['t-shirts-polos', ['T-shirts & Polos', 'T-shirts & Polos', 'تيشيرتات وبولو'], [
                ['basics', ['Basiques', 'Basics', 'أساسيات']],
                ['printed', ['Imprimés', 'Printed', 'مطبوعات']],
            ]],
            ['pants-chinos', ['Pantalons & Chinos', 'Pants & Chinos', 'بناطيل وشينو'], []],
            ['jeans', ['Jeans', 'Jeans', 'جينز'], [
                ['skinny', ['Skinny', 'Skinny', 'ضيق']],
                ['straight', ['Droit', 'Straight', 'مستقيم']],
                ['regular', ['Regular', 'Regular', 'عادي']],
            ]],
            ['jackets-coats', ['Vestes & Manteaux', 'Jackets & Coats', 'جاكيتات ومعاطف'], [
                ['bombers', ['Bombers', 'Bombers', 'بومبر']],
                ['jackets', ['Vestes', 'Jackets', 'جاكيتات']],
                ['trench-coats', ['Trenchs', 'Trench coats', 'معاطف ترانش']],
            ]],
            ['sweaters', ['Pulls', 'Sweaters', 'سترات صوفية'], []],
            ['sweatshirts', ['Sweats', 'Sweatshirts', 'سويت شيرت'], []],
            ['hoodies', ['Hoodies', 'Hoodies', 'هوديز'], []],
            ['sportswear', ['Sportswear', 'Sportswear', 'ملابس رياضية'], []],
            ['shoes', ['Chaussures', 'Shoes', 'أحذية'], []],
            ['sneakers', ['Sneakers', 'Sneakers', 'سنيكرز'], []],
            ['sandals', ['Sandales', 'Sandals', 'صنادل'], []],
            ['accessories', ['Accessoires', 'Accessories', 'إكسسوارات'], []],
            ['bags', ['Sacs', 'Bags', 'حقائب'], [
                ['backpacks', ['Sacs à dos', 'Backpacks', 'حقائب ظهر']],
                ['wallets', ['Portefeuilles', 'Wallets', 'محافظ']],
            ]],
            ['caps-hats', ['Casquettes & Chapeaux', 'Caps & Hats', 'قبعات'], []],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->tree() as $sortOrder => [$slug, $names, $children]) {
            $parent = Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => ['fr' => $names[0], 'en' => $names[1], 'ar' => $names[2]],
                    'sort_order' => $sortOrder,
                    'is_active' => true,
                ]
            );

            foreach ($children as $childSortOrder => [$childSlug, $childNames]) {
                Category::updateOrCreate(
                    ['slug' => $childSlug],
                    [
                        'parent_id' => $parent->id,
                        'name' => ['fr' => $childNames[0], 'en' => $childNames[1], 'ar' => $childNames[2]],
                        'sort_order' => $childSortOrder,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
