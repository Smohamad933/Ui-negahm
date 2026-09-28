<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'site_name',
        'tagline',
        'logo_url',
        'favicon_url',
        'color_bg',
        'color_fg',
        'color_primary',
        'color_secondary',
        'color_accent',
        'color_muted',
        'font_family',
        'hero_title',
        'hero_subtitle',
        'hero_cta_text',
        'hero_cta_link',
        'hero_media_url',
        'about_title',
        'about_body',
        'about_image_url',
        'contact_address',
        'contact_phone',
        'contact_email',
        'contact_map_embed',
        'social_instagram',
        'social_telegram',
        'social_whatsapp',
        'social_linkedin',
        'footer_text',
        'updated_at',
    ];

    /**
     * There is always exactly one settings row (id = 1).
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }
}
