<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'compare_at_price',
        'sku',
        'is_active',
        'is_featured',
        'is_on_podium',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:3',
            'compare_at_price' => 'decimal:3',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_on_podium' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class)->orderBy('sort_order');
    }

    public function colors(): HasMany
    {
        return $this->hasMany(ProductColor::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeSearchName($query, string $search)
    {
        return $query->where(function ($query) use ($search) {
            foreach (['fr', 'en', 'ar'] as $locale) {
                $query->orWhereLike('name->'.$locale, '%'.$search.'%');
            }
        });
    }

    public function primaryImage(): ?ProductImage
    {
        return $this->images->firstWhere('is_primary', true) ?? $this->images->first();
    }

    public function totalStock(): int
    {
        return $this->variants->sum('stock_quantity');
    }

    public function inStock(): bool
    {
        return $this->totalStock() > 0;
    }

    /**
     * The variant used for one-click "quick add" from a product card: the first
     * size/color combination (by admin-defined sort order) that still has stock.
     */
    public function defaultVariant(): ?ProductVariant
    {
        $sorted = $this->variants->sortBy(fn (ProductVariant $v) => [
            $v->size?->sort_order ?? 0,
            $v->color?->sort_order ?? 0,
        ]);

        return $sorted->first(fn (ProductVariant $v) => $v->stock_quantity > 0) ?? $sorted->first();
    }

    public function imageFor(?ProductColor $color = null): ?ProductImage
    {
        if ($color) {
            $match = $this->images->firstWhere('product_color_id', $color->id);
            if ($match) {
                return $match;
            }
        }

        return $this->primaryImage();
    }
}
