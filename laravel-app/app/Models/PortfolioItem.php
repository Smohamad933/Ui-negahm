<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioItem extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'media_url',
        'media_type',
        'order_index',
        'featured_home',
    ];

    protected $casts = [
        'featured_home' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
