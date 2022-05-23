<?php

namespace abdelrhmanSaeed\JwtGuard\Database\Factories;

use App\Models\User;
use abdelrhmanSaeed\JwtGuard\Database\Models\RefreshToken;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RefreshTokenFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = RefreshToken::class;

    public function definition()
    {
        return [
            'uuid' => Str::uuid(),
            'user_id' => User::factory()->create()->id,
            'expire_at' => now()->addHours(3)
        ];
    }
}
