<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Font extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'family_name',
        'weight',
        'style',
        'format',
        'file_url',
    ];
}
