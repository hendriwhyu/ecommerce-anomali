<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, HasUuids;

    // protected $casts = [
    //     'category_id' => 'array',
    // ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image',
    ];

    public function setNameAttribute($value){
    $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function categories(): BelongsToMany {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id');
    }

    public function items(): HasMany{
        return $this->hasMany(OrderItem::class);
    }

}
