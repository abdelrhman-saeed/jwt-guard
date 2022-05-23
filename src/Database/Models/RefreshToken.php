<?php

namespace abdelrhmanSaeed\JwtGuard\Database\Models;

use abdelrhmanSaeed\JwtGuard\Database\Factories\RefreshTokenFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{
    use HasFactory;

    protected $fillable = ['expire_at', 'user_id', 'uuid'];
    protected $casts    = ['uuid' => 'string'];

    protected static function newFactory()
    {
        return RefreshTokenFactory::new();
    }
}