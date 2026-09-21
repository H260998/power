<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PodiumTest extends TestCase
{
    use RefreshDatabase;

    private function product(string $slug, array $attributes = [], bool $image = true): Product
    {
        $category = Category::firstOrCreate(['slug' => 'jeans'], ['name' => ['fr' => 'Jeans', 'en' => 'Jeans', 'ar' => 'جينز']]);
        $product = Product::create([
            'category_id' => $category->id, 'slug' => $slug,
            'name' => ['fr' => $slug, 'en' => $slug, 'ar' => $slug],
            'price' => 40, 'is_active' => true, ...$attributes,
        ]);
        if ($image) {
            $product->images()->create(['path' => 'products/'.$slug.'.webp', 'is_primary' => true]);
        }

        return $product;
    }

    public function test_podium_uses_selected_active_products_and_their_main_photos(): void
    {
        $first = $this->product('veste-teddy-noire-blanche-boston', ['is_on_podium' => true]);
        $second = $this->product('my-cap', ['is_on_podium' => true]);
        $first->images()->update(['is_primary' => false]);
        $first->images()->create(['path' => 'https://example.public.blob.vercel-storage.com/main.webp', 'is_primary' => true]);
        $this->product('not-selected', ['is_featured' => true]);
        $this->product('inactive', ['is_on_podium' => true, 'is_active' => false]);
        $this->product('no-photo', ['is_on_podium' => true], false);
        $this->product('deleted', ['is_on_podium' => true])->delete();

        $response = $this->get('/')->assertOk();
        $slides = $response->viewData('heroSlides');
        $this->assertCount(2, $slides);
        $this->assertSame('https://example.public.blob.vercel-storage.com/main.webp', $slides[0]['image']);
        $this->assertSame(route('shop.product', $first->slug), $slides[0]['url']);
        $this->assertSame($second->name, $slides[1]['name']);
        $this->assertSame(1.3, $slides[0]['scale']);
        $this->assertSame(1, $slides[1]['scale']);
        $response->assertSee('id="hero-product-img"', false);
    }

    public function test_no_selection_leaves_the_podium_empty_even_for_demo_or_featured_products(): void
    {
        $this->product('classic-snapback-cap', ['is_featured' => true]);
        $response = $this->get('/')->assertOk();
        $this->assertCount(0, $response->viewData('heroSlides'));
        $response->assertDontSee('id="hero-product-img"', false);
    }

    public function test_admin_can_select_and_deselect_the_podium_independently_of_featured(): void
    {
        $admin = Admin::create(['name' => 'Admin', 'email' => 'podium@example.test', 'password' => 'test-password-only']);
        $category = Category::create(['slug' => 'caps', 'name' => ['fr' => 'Casquettes']]);
        $data = [
            'category_id' => $category->id, 'slug' => 'new-podium-product',
            'name' => ['fr' => 'Casquette', 'en' => 'Cap', 'ar' => 'قبعة'],
            'price' => 27, 'is_active' => '1', 'is_featured' => '0', 'is_on_podium' => '1',
        ];
        $this->actingAs($admin, 'admin')->post('/admin/products', $data)->assertSessionHasNoErrors();
        $product = Product::where('slug', $data['slug'])->firstOrFail();
        $this->assertTrue($product->is_on_podium);
        $this->assertFalse($product->is_featured);
        $this->get('/admin/products/'.$product->id.'/edit')->assertOk()->assertSee(__('admin.on_podium'));

        $data['is_on_podium'] = '0';
        $data['is_featured'] = '1';
        $this->put('/admin/products/'.$product->id, $data)->assertSessionHasNoErrors();
        $this->assertFalse($product->refresh()->is_on_podium);
        $this->assertTrue($product->is_featured);
        $this->assertSame('27.000', $product->price);
    }
}
