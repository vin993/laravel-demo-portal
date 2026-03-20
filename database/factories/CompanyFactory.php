<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'legal_name' => $this->faker->company() . ' ' . $this->faker->companySuffix,
            'tax_number' => $this->faker->numerify('TAX###########'),
            'registration_number' => $this->faker->numerify('REG###########'),
            'email' => $this->faker->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'website' => $this->faker->domainName(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->numberBetween(1, 10),
            'state' => $this->faker->numberBetween(1, 10),
            'country' => $this->faker->numberBetween(1, 10),
            'postal_code' => $this->faker->postcode(),
            'logo' => $this->faker->imageUrl(640, 480, 'business'),
            'currency' => $this->faker->currencyCode(),
            'timezone' => $this->faker->randomElement(['UTC', 'America/New_York', 'Europe/London', 'Asia/Tokyo']),
            'about' => $this->faker->paragraph(3),
        ];
    }
}