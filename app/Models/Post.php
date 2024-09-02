<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'content',
        'thumbnail',
        'category_id'
    ];


    public function setNameAttribute($value){
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function categories(): BelongsToMany{
        return $this->belongsToMany(Category::class, 'category_post', 'post_id', 'category_id');
    }

    public function author(): BelongsTo{
        return $this->belongsTo(User::class, 'author_id');
    }
}
