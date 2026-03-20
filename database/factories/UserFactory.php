<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $isApproved = $this->faker->boolean(80);

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role_id' => Role::factory(),
            'is_approved' => $isApproved,
            'is_active' => $this->faker->boolean(90),
            'approved_at' => $isApproved ? now() : null,
            'approved_by' => $isApproved ? Role::where('name', 'Admin')->first()->users->random()->id : null,
            'remember_token' => Str::random(10),
        ];
    }
}