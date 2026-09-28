<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'logo_url',
        'cover_image_url',
        'accent_color',
        'short_description',
        'industry',
        'website_url',
        'year',
        'featured',
        'published',
        'order_index',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'published' => 'boolean',
    ];

    public function categories()
    {
        return $this->hasMany(Category::class)->orderBy('order_index');
    }

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index');
    }
}
