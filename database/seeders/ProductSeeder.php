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
            [
                'slug' => 'polo-marine-classique',
                'category' => 'basics',
                'image' => 'products/polo-marine-classique.webp',
                'price' => 49.900,
                'compare_at_price' => null,
                'name' => ['fr' => 'Polo Marine Classique', 'en' => 'Classic Navy Polo', 'ar' => 'قميص بولو كحلي كلاسيكي'],
                'description' => [
                    'fr' => 'Polo bleu marine à manches courtes avec liserés blancs au col et aux manches. Une coupe confortable et élégante au quotidien.',
                    'en' => 'Navy short-sleeve polo with white piping on the collar and cuffs. A comfortable, polished everyday fit.',
                    'ar' => 'قميص بولو كحلي بأكمام قصيرة وحواف بيضاء عند الياقة والأكمام، بقصة مريحة وأنيقة.',
                ],
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => [['Bleu Marine', '#172554']],
                'featured' => true,
                'podium' => false,
            ],
            [
                'slug' => 'chino-vert-fonce',
                'category' => 'pants-chinos',
                'image' => 'products/chino-vert-fonce.webp',
                'price' => 69.900,
                'compare_at_price' => null,
                'name' => ['fr' => 'Chino Vert Foncé', 'en' => 'Dark Green Chinos', 'ar' => 'بنطال تشينو أخضر داكن'],
                'description' => [
                    'fr' => 'Pantalon chino vert foncé à coupe ajustée, avec poches latérales et finition sobre pour une tenue habillée ou décontractée.',
                    'en' => 'Slim dark green chinos with side pockets and a clean finish for smart or casual outfits.',
                    'ar' => 'بنطال تشينو أخضر داكن بقصة ضيقة وجيوب جانبية، مناسب للإطلالات الرسمية واليومية.',
                ],
                'sizes' => ['38', '40', '42', '44', '46'],
                'colors' => [['Vert Foncé', '#18312B']],
                'featured' => false,
                'podium' => false,
            ],
            [
                'slug' => 'pantalon-habille-noir',
                'category' => 'pants-chinos',
                'image' => 'products/pantalon-habille-noir.webp',
                'price' => 79.900,
                'compare_at_price' => 89.900,
                'name' => ['fr' => 'Pantalon Habillé Noir', 'en' => 'Black Tailored Trousers', 'ar' => 'بنطال رسمي أسود'],
                'description' => [
                    'fr' => 'Pantalon noir habillé à plis marqués et coupe fuselée. Idéal avec une chemise, un polo ou une veste.',
                    'en' => 'Black tailored trousers with pressed creases and a tapered fit. Easy to style with a shirt, polo or jacket.',
                    'ar' => 'بنطال رسمي أسود بثنيات واضحة وقصة مدببة، مناسب مع القميص أو البولو أو السترة.',
                ],
                'sizes' => ['38', '40', '42', '44', '46'],
                'colors' => [['Noir', '#111111']],
                'featured' => false,
                'podium' => false,
            ],
            [
                'slug' => 'jean-cargo-bleu',
                'category' => 'straight',
                'image' => 'products/jean-cargo-bleu.webp',
                'price' => 84.900,
                'compare_at_price' => 94.900,
                'name' => ['fr' => 'Jean Cargo Bleu', 'en' => 'Blue Cargo Jeans', 'ar' => 'جينز كارغو أزرق'],
                'description' => [
                    'fr' => 'Jean cargo bleu à coupe droite avec grandes poches latérales. Une silhouette utilitaire pensée pour le streetwear.',
                    'en' => 'Straight-leg blue cargo jeans with roomy side pockets. A utility silhouette made for streetwear.',
                    'ar' => 'جينز كارغو أزرق بقصة مستقيمة وجيوب جانبية كبيرة، بتصميم عملي للستريت وير.',
                ],
                'sizes' => ['28', '30', '32', '34', '36'],
                'colors' => [['Bleu Denim', '#6B8EAD']],
                'featured' => true,
                'podium' => false,
            ],
            [
                'slug' => 'casquette-noire-essentielle',
                'category' => 'caps-hats',
                'image' => 'products/casquette-noire-essentielle.webp',
                'price' => 24.900,
                'compare_at_price' => null,
                'name' => ['fr' => 'Casquette Noire Essentielle', 'en' => 'Essential Black Cap', 'ar' => 'قبعة سوداء أساسية'],
                'description' => [
                    'fr' => 'Casquette noire unie à visière courbée et fermeture réglable. Un accessoire minimaliste facile à assortir.',
                    'en' => 'Plain black cap with a curved visor and adjustable fastening. A versatile minimalist accessory.',
                    'ar' => 'قبعة سوداء سادة بواقٍ منحني وإغلاق قابل للتعديل، إكسسوار بسيط وسهل التنسيق.',
                ],
                'sizes' => ['Taille unique'],
                'colors' => [['Noir', '#111111']],
                'featured' => false,
                'podium' => false,
            ],
            [
                'slug' => 'casquette-trucker-bordeaux',
                'category' => 'caps-hats',
                'image' => 'products/casquette-trucker-bordeaux.webp',
                'price' => 34.900,
                'compare_at_price' => null,
                'name' => ['fr' => 'Casquette Trucker Bordeaux', 'en' => 'Burgundy Trucker Cap', 'ar' => 'قبعة تراكر عنابية'],
                'description' => [
                    'fr' => 'Casquette trucker bordeaux avec empiècement en mesh respirant et écusson graphique sur le devant.',
                    'en' => 'Burgundy trucker cap with a breathable mesh back and a graphic front patch.',
                    'ar' => 'قبعة تراكر عنابية بخلفية شبكية جيدة التهوية وشعار رسومي في الأمام.',
                ],
                'sizes' => ['Taille unique'],
                'colors' => [['Bordeaux', '#7F1D3A']],
                'featured' => false,
                'podium' => false,
            ],
            [
                'slug' => 'sneakers-cuir-noir',
                'category' => 'sneakers',
                'image' => 'products/sneakers-cuir-noir.webp',
                'price' => 119.900,
                'compare_at_price' => 139.900,
                'name' => ['fr' => 'Sneakers en Cuir Noir', 'en' => 'Black Leather Sneakers', 'ar' => 'حذاء رياضي جلد أسود'],
                'description' => [
                    'fr' => 'Sneakers basses en cuir noir grainé avec semelle blanche contrastante. Un modèle épuré pour toutes les tenues.',
                    'en' => 'Low-top sneakers in textured black leather with a contrasting white sole. A clean style for every outfit.',
                    'ar' => 'حذاء رياضي منخفض من الجلد الأسود المحبب بنعل أبيض متباين، تصميم أنيق لكل الإطلالات.',
                ],
                'sizes' => ['39', '40', '41', '42', '43', '44'],
                'colors' => [['Noir', '#111111']],
                'featured' => true,
                'podium' => false,
            ],
            [
                'slug' => 'sneakers-plateforme-blanc-noir',
                'category' => 'sneakers',
                'image' => 'products/sneakers-plateforme-blanc-noir.webp',
                'price' => 109.900,
                'compare_at_price' => 129.900,
                'name' => ['fr' => 'Sneakers Plateforme Blanc & Noir', 'en' => 'White & Black Platform Sneakers', 'ar' => 'حذاء رياضي منصة أبيض وأسود'],
                'description' => [
                    'fr' => 'Sneakers blanches à détails noirs, lacets contrastants et semelle plateforme confortable pour une allure moderne.',
                    'en' => 'White sneakers with black details, contrasting laces and a comfortable platform sole for a modern look.',
                    'ar' => 'حذاء رياضي أبيض بتفاصيل ورباط أسود ونعل منصة مريح لإطلالة عصرية.',
                ],
                'sizes' => ['36', '37', '38', '39', '40', '41'],
                'colors' => [['Blanc & Noir', '#F5F5F4']],
                'featured' => true,
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
