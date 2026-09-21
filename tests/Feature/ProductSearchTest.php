<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_matches_translations_without_bypassing_the_active_filter(): void
    {
        $category = Category::create(['name' => ['fr' => 'Chaussures'], 'slug' => 'shoes']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => ['fr' => 'Chaussure Bleue', 'en' => 'Blue Sneaker', 'ar' => 'حذاء أزرق'],
            'slug' => 'blue-sneaker', 'price' => 25, 'is_active' => true,
        ]);
        Product::create([
            'category_id' => $category->id,
            'name' => ['fr' => 'Autre', 'en' => 'Blue Hidden', 'ar' => 'مخفي'],
            'slug' => 'hidden', 'price' => 20, 'is_active' => false,
        ]);
        foreach (['bleue', 'SNEAKER', 'أزرق'] as $term) {
            $this->assertSame([$product->id], Product::active()->searchName($term)->pluck('id')->all());
        }
        $this->assertSame([$product->id], Product::active()->searchName('Blue')->pluck('id')->all());
        $this->get('/boutique?q=Blue')->assertOk()->assertSee('Blue Sneaker')->assertDontSee('Blue Hidden');
    }
}
