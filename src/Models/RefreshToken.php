<?php

namespace abdelrhmanSaeed\JwtGuard\Models;

use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{
    protected $fillable = ['expire_at', 'user_id', 'uuid'];
    protected $casts    = ['uuid' => 'string'];
}