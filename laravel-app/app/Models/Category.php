<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    const UPDATED_AT = null;

    public const ASPECT_RATIOS = ['16:9', '9:16', '1:1'];

    protected $fillable = [
        'client_id',
        'title',
        'slug',
        'description',
        'cover_image_url',
        'aspect_ratio',
        'order_index',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(PortfolioItem::class)->orderBy('order_index');
    }

    public function normalizedAspectRatio(): string
    {
        return in_array($this->aspect_ratio, self::ASPECT_RATIOS, true) ? $this->aspect_ratio : '16:9';
    }
}
