<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
    protected $table = 'admin_users';

    public $timestamps = false;

    protected $fillable = [
        'username',
        'password_hash',
        'name',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * The password column in this legacy schema is "password_hash", not "password".
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }
}
