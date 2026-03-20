<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RegularUserSeeder extends Seeder
{
    public function run()
    {
        $companies = [
            'Tech Solutions Ltd',
            'Digital Dynamics',
            'Smart Electronics',
            'Power Systems Inc',
            'Circuit Masters',
            'Component Hub',
            'Electronic Experts',
            'Tech Hardware Pro',
            'Power Solutions',
            'Global Electronics',
            'Circuit Innovations',
            'Smart Components',
            'Digital Masters',
            'Tech Dynamics',
            'Electronic Hub',
            'Power Electronics',
            'Component Experts',
            'Circuit Solutions',
            'Smart Systems',
            'Global Components'
        ];

        for ($i = 0; $i < 50; $i++) {
            $user = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role_id' => 2,
                'is_approved' => true,
                'approved_by' => 2,
                'is_active' => true,
            ]);

            UserDetail::create([
                'user_id' => $user->id,
                'company_name' => $companies[array_rand($companies)] . ' ' . fake()->companySuffix(),
                'dealer_name' => fake()->name(),
                'phone' => fake()->phoneNumber(),
                'address' => fake()->streetAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'postal_code' => fake()->postcode(),
                'country' => 'United States'
            ]);
        }
    }
}