<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Services\ImageUploadService;
use App\Services\StockService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class ProductSeeder extends Seeder
{
    /**
     * Demo catalog seeded from the design prototype's own product photography
     * (assets/product-*.png), carried over onto the real category tree.
     */
    private function catalog(): array
    {
        return [
            [
                'slug' => 'classic-snapback-cap',
                'category' => 'caps-hats',
                'source' => 'product-cap.webp',
                'price' => 39.900,
                'name' => ['fr' => 'Casquette Snapback Classique', 'en' => 'Classic Snapback Cap', 'ar' => 'قبعة سناباك كلاسيكية'],
                'description' => [
                    'fr' => "Une casquette snapback nette et structurée, finie avec un logo brodé audacieux. Sangle ajustable pour un maintien parfait.",
                    'en' => 'A sharp, structured snapback finished with a bold embroidered logo. Adjustable strap for a locked-in fit.',
                    'ar' => 'قبعة سناباك أنيقة ومهيكلة بشعار مطرز بارز. حزام قابل للتعديل لملاءمة مثالية.',
                ],
                'sizes' => ['One Size'],
                'colors' => [['Noir', '#111111'], ['Bleu Marine', '#1E293B']],
                'featured' => true,
            ],
            [
                'slug' => 'oversized-graphic-tee',
                'category' => 'printed',
                'source' => 'product-tee.png',
                'price' => 44.900,
                'name' => ['fr' => 'T-shirt Graphique Oversize "Magic"', 'en' => '"Magic" Oversized Graphic Tee', 'ar' => 'تيشيرت أوفرسايز مطبوع "Magic"'],
                'description' => [
                    'fr' => "T-shirt oversize en coton épais avec un imprimé graphique dessiné à la main sur toute la surface. Un essentiel streetwear, pensé pour votre force au quotidien.",
                    'en' => 'Heavyweight cotton oversized tee with an all-over hand-drawn graphic print. Streetwear essential, built for everyday power.',
                    'ar' => 'تيشيرت أوفرسايز من القطن الثقيل بطبعة رسومية مرسومة يدويًا. أساسي ستريتوير لقوتك اليومية.',
                ],
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => [['Noir', '#111111'], ['Blanc', '#F5F5F4']],
                'featured' => true,
            ],
            [
                'slug' => 'uptempo-retro-sneaker',
                'category' => 'sneakers',
                'source' => 'product-sneaker.png',
                'price' => 189.900,
                'name' => ['fr' => 'Sneaker Rétro Uptempo', 'en' => 'Uptempo Retro Sneaker', 'ar' => 'حذاء سنيكرز ريترو أوبتيمبو'],
                'description' => [
                    'fr' => "Sneaker de basketball rétro montante avec un branding contrasté audacieux et une semelle à air amortie pour un confort toute la journée.",
                    'en' => 'High-top retro basketball sneaker with bold blocked branding and a cushioned air sole for all-day comfort.',
                    'ar' => 'حذاء كرة سلة ريترو عالي الرقبة بعلامة تجارية جريئة ونعل هوائي مبطن لراحة طوال اليوم.',
                ],
                'sizes' => ['40', '41', '42', '43', '44', '45'],
                'colors' => [['Blanc', '#F5F5F4'], ['Noir', '#111111']],
                'featured' => true,
            ],
            [
                'slug' => 'varsity-letterman-jacket',
                'category' => 'jackets',
                'source' => 'product-jacket.png',
                'price' => 129.900,
                'name' => ['fr' => 'Veste Varsity Teddy', 'en' => 'Varsity Letterman Jacket', 'ar' => 'جاكيت فارسيتي'],
                'description' => [
                    'fr' => "Veste varsity bicolore classique avec col, poignets et ourlet côtelés, et un patch chenille sur la poitrine. Une pièce qui affirme votre style.",
                    'en' => 'Classic two-tone varsity jacket with ribbed collar, cuffs and hem, and a chenille chest patch. A statement layer.',
                    'ar' => 'جاكيت فارسيتي كلاسيكي بلونين مع ياقة وأكمام مضلعة ورقعة صدر مخملية. قطعة تعبّر عن أسلوبك.',
                ],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => [['Noir & Crème', '#111111']],
                'featured' => true,
            ],
            [
                'slug' => 'essential-sweatshorts',
                'category' => 'sportswear',
                'source' => 'product-shorts.png',
                'price' => 49.900,
                'name' => ['fr' => 'Short Molleton Essentiel', 'en' => 'Essential Sweatshorts', 'ar' => 'شورت رياضي أساسي'],
                'description' => [
                    'fr' => "Short en molleton doux avec taille élastique à cordon et poches latérales. Coupe confort pour l'entraînement ou la détente.",
                    'en' => 'Soft fleece sweatshorts with an elastic drawstring waist and side pockets. Comfort-fit for training or lounging.',
                    'ar' => 'شورت من الصوف الناعم بخصر مطاطي برباط وجيوب جانبية. مقاس مريح للتمرين أو الاسترخاء.',
                ],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => [['Gris Chiné', '#9CA3AF'], ['Noir', '#111111']],
                'featured' => false,
            ],
            [
                'slug' => 'ripped-mom-fit-jeans',
                'category' => 'regular',
                'source' => 'product-jeans.png',
                'price' => 69.900,
                'name' => ['fr' => 'Jean Mom-Fit Déchiré', 'en' => 'Ripped Mom-Fit Jeans', 'ar' => 'جينز مقاس مام ممزق'],
                'description' => [
                    'fr' => "Denim délavé clair avec détails déchirés et une coupe mom-fit détendue. Finitions avec une quincaillerie de marque premium.",
                    'en' => 'Light-wash denim with distressed detailing and a relaxed mom-fit taper. Finished with premium branded hardware.',
                    'ar' => 'دنيم فاتح بتفاصيل ممزقة وقصة مام مريحة. تشطيبات بإكسسوارات مميزة.',
                ],
                'sizes' => ['28', '30', '32', '34', '36'],
                'colors' => [['Bleu Clair', '#93C5FD']],
                'featured' => false,
            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $images = app(ImageUploadService::class);
        $stock = app(StockService::class);
        $sourceDir = base_path('assets');

        foreach ($this->catalog() as $sortOrder => $item) {
            $category = Category::where('slug', $item['category'])->first();

            if (! $category) {
                continue;
            }

            $product = Product::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'category_id' => $category->id,
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'is_active' => true,
                    'is_featured' => $item['featured'],
                ]
            );

            if ($product->sizes()->count() === 0) {
                foreach ($item['sizes'] as $order => $label) {
                    $product->sizes()->create(['label' => $label, 'sort_order' => $order]);
                }
            }

            if ($product->colors()->count() === 0) {
                foreach ($item['colors'] as $order => [$name, $hex]) {
                    $product->colors()->create(['name' => $name, 'hex_code' => $hex, 'sort_order' => $order]);
                }
            }

            $stock->syncVariants($product->fresh(['sizes', 'colors']));

            // Give every variant a modest, varied stock count so the storefront and dashboard have real numbers to show.
            $product->variants()->get()->each(
                fn ($variant, $i) => $variant->stock_quantity === 0
                    ? $variant->update(['stock_quantity' => 8 + ($i * 3) % 20])
                    : null
            );

            if ($product->images()->count() === 0) {
                $sourcePath = $sourceDir.'/'.$item['source'];

                if (is_file($sourcePath)) {
                    $path = $images->store(
                        new UploadedFile($sourcePath, $item['source'], null, null, true),
                        'products'
                    );

                    $product->images()->create([
                        'path' => $path,
                        'sort_order' => 0,
                        'is_primary' => true,
                    ]);
                }
            }
        }
    }
}
