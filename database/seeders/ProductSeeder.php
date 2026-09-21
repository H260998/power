<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * The production catalog. Product images are committed under
     * public/uploads/products so they remain available on Vercel.
     */
    private function catalog(): array
    {
        return [
            [
                'slug' => 'short-jean-delave-gris',
                'category' => 'jeans',
                'image' => 'products/short-jean-delave.webp',
                'price' => 59.900,
                'compare_at_price' => 69.900,
                'name' => ['fr' => 'Short en Jean Délavé Gris', 'en' => 'Grey Washed Denim Shorts', 'ar' => 'شورت جينز رمادي مغسول'],
                'description' => [
                    'fr' => 'Short en denim gris délavé avec détails usés et coupe droite confortable. Une pièce streetwear facile à porter au quotidien.',
                    'en' => 'Grey washed denim shorts with distressed details and a comfortable straight fit. An easy everyday streetwear piece.',
                    'ar' => 'شورت جينز رمادي مغسول بتفاصيل ممزقة وقصة مستقيمة مريحة، مناسب للإطلالات اليومية.',
                ],
                'sizes' => ['28', '30', '32', '34', '36'],
                'colors' => [['Gris Délavé', '#4B5563']],
                'featured' => true,
                'podium' => false,
            ],
            [
                'slug' => 'classic-snapback-cap',
                'category' => 'caps-hats',
                'image' => 'products/casquette-ny-noire.webp',
                'price' => 39.900,
                'compare_at_price' => null,
                'name' => ['fr' => 'Casquette NY Noire', 'en' => 'Black NY Cap', 'ar' => 'قبعة نيويورك سوداء'],
                'description' => [
                    'fr' => 'Casquette noire structurée avec logo NY blanc brodé et sangle réglable pour un ajustement confortable.',
                    'en' => 'Structured black cap with an embroidered white NY logo and an adjustable strap for a comfortable fit.',
                    'ar' => 'قبعة سوداء مهيكلة بشعار نيويورك أبيض مطرز وحزام قابل للتعديل.',
                ],
                'sizes' => ['Taille unique'],
                'colors' => [['Noir', '#111111']],
                'featured' => true,
                'podium' => false,
            ],
            [
                'slug' => 'oversized-graphic-tee',
                'category' => 'printed',
                'image' => 'products/tshirt-magic-noir.webp',
                'price' => 44.900,
                'compare_at_price' => 54.900,
                'name' => ['fr' => 'T-shirt Oversize Magic', 'en' => 'Magic Oversized T-shirt', 'ar' => 'تيشيرت ماجيك أوفرسايز'],
                'description' => [
                    'fr' => 'T-shirt oversize noir en coton avec grand imprimé graphique coloré au dos. Coupe ample et esprit streetwear.',
                    'en' => 'Black oversized cotton T-shirt with a large colorful back graphic. Relaxed fit with a streetwear attitude.',
                    'ar' => 'تيشيرت قطني أسود أوفرسايز بطبعة خلفية كبيرة وملونة وقصة مريحة.',
                ],
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => [['Noir', '#111111']],
                'featured' => true,
                'podium' => true,
            ],
            [
                'slug' => 'uptempo-retro-sneaker',
                'category' => 'sneakers',
                'image' => 'products/sneaker-uptempo-rouge.webp',
                'price' => 189.900,
                'compare_at_price' => 219.900,
                'name' => ['fr' => 'Sneaker Uptempo Rouge', 'en' => 'Red Uptempo Sneaker', 'ar' => 'حذاء أوبتيمبو أحمر'],
                'description' => [
                    'fr' => 'Sneaker montante rouge, noire et blanche au style basketball rétro, avec semelle amortissante pour un confort durable.',
                    'en' => 'Red, black and white high-top sneaker with retro basketball styling and a cushioned sole for lasting comfort.',
                    'ar' => 'حذاء رياضي مرتفع بالأحمر والأسود والأبيض بتصميم كرة سلة كلاسيكي ونعل مريح.',
                ],
                'sizes' => ['40', '41', '42', '43', '44', '45'],
                'colors' => [['Rouge & Noir', '#DC2626']],
                'featured' => true,
                'podium' => true,
            ],
            [
                'slug' => 'veste-teddy-noire-blanche-boston',
                'category' => 'jackets',
                'image' => 'products/veste-varsity-boston.webp',
                'price' => 129.900,
                'compare_at_price' => 149.900,
                'name' => ['fr' => 'Veste Teddy Boston', 'en' => 'Boston Varsity Jacket', 'ar' => 'جاكيت بوسطن فارسيتي'],
                'description' => [
                    'fr' => 'Veste varsity noire et blanche avec écussons Boston, finitions côtelées et fermeture à boutons pression.',
                    'en' => 'Black and white varsity jacket with Boston patches, ribbed trims and snap-button fastening.',
                    'ar' => 'جاكيت فارسيتي بالأسود والأبيض مع شارات بوسطن وحواف مضلعة وأزرار كبس.',
                ],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => [['Noir & Blanc', '#111111']],
                'featured' => true,
                'podium' => true,
            ],
            [
                'slug' => 'essential-blue-sweatshorts',
                'category' => 'sportswear',
                'image' => 'products/short-molleton-bleu.webp',
                'price' => 49.900,
                'compare_at_price' => null,
                'name' => ['fr' => 'Short Molleton Bleu Power', 'en' => 'Power Blue Sweatshorts', 'ar' => 'شورت باور أزرق'],
                'description' => [
                    'fr' => 'Short bleu en molleton doux avec taille élastique, cordon de serrage et étiquette Power. Confortable pour le sport et la détente.',
                    'en' => 'Soft blue sweatshorts with an elastic waist, drawstring and Power label. Comfortable for training and downtime.',
                    'ar' => 'شورت أزرق ناعم بخصر مطاطي ورباط وشعار باور، مناسب للرياضة والراحة.',
                ],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'colors' => [['Bleu', '#3B82F6']],
                'featured' => false,
                'podium' => false,
            ],
            [
                'slug' => 'ripped-mom-fit-jeans',
                'category' => 'regular',
                'image' => 'products/jean-mom-fit-bleu.webp',
                'price' => 69.900,
                'compare_at_price' => 79.900,
                'name' => ['fr' => 'Jean Mom-Fit Déchiré', 'en' => 'Ripped Mom-Fit Jeans', 'ar' => 'جينز مام فت ممزق'],
                'description' => [
                    'fr' => 'Jean bleu clair à taille haute avec détails déchirés et coupe mom-fit décontractée. Une base moderne pour toutes les saisons.',
                    'en' => 'High-waisted light blue jeans with distressed details and a relaxed mom fit. A modern staple for every season.',
                    'ar' => 'جينز أزرق فاتح بخصر مرتفع وتفاصيل ممزقة وقصة مام فت مريحة.',
                ],
                'sizes' => ['28', '30', '32', '34', '36'],
                'colors' => [['Bleu Clair', '#93C5FD']],
                'featured' => false,
                'podium' => false,
            ],
        ];
    }

    public function run(): void
    {
        $stock = app(StockService::class);

        foreach ($this->catalog() as $item) {
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
                    'compare_at_price' => $item['compare_at_price'],
                    'is_active' => true,
                    'is_featured' => $item['featured'],
                    'is_on_podium' => $item['podium'],
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

            $product->variants()->get()->each(
                fn ($variant, $index) => $variant->stock_quantity === 0
                    ? $variant->update(['stock_quantity' => 12 + ($index * 3) % 16])
                    : null
            );

            $product->images()->updateOrCreate(
                ['sort_order' => 0],
                [
                    'path' => $item['image'],
                    'is_primary' => true,
                    'product_color_id' => $product->colors()->value('id'),
                ]
            );
        }
    }
}
