<?php

namespace Database\Factories;

use App\Models\Industry;
use Illuminate\Database\Eloquent\Factories\Factory;

class IndustryFactory extends Factory
{
    protected $model = Industry::class;

    public function definition()
    {
        $industries = [
            'Agricultural Tractors',
            'Harvesters',
            'Construction Equipment',
            'Mining Equipment',
            'Marine Demo App',
            'Power Generation',
            'Farm Implements',
            'Irrigation Systems',
            'Forestry Equipment',
            'Material Handling',
            'Industrial Demo App',
            'Agricultural Sprayers',
            'Tillage Equipment',
            'Seed Drills',
            'Combine Harvesters'
        ];

        $descriptions = [
            'Agricultural Tractors' => 'Equipment used for farming operations including plowing, tilling, planting, and transportation',
            'Harvesters' => 'Specialized machinery for harvesting various types of crops',
            'Construction Equipment' => 'Heavy machinery used in construction with specialized power requirements',
            'Mining Equipment' => 'Heavy-duty equipment used in mining operations',
            'Marine Solutions' => 'Solutions specifically designed for marine applications and boats',
            'Power Generation' => 'Systems for electrical power generation and backup power',
            'Farm Implements' => 'Various farming tools and equipment that work with tractors',
            'Irrigation Systems' => 'Equipment for water distribution in agricultural settings',
            'Forestry Equipment' => 'Specialized machinery for forest management and logging',
            'Material Handling' => 'Equipment for moving, storing, and handling materials',
            'Industrial Equipment' => 'Heavy-duty equipment for industrial applications',
            'Agricultural Sprayers' => 'Equipment for applying fertilizers and crop protection products',
            'Tillage Equipment' => 'Machinery for soil preparation and cultivation',
            'Seed Drills' => 'Precision equipment for planting seeds',
            'Combine Harvesters' => 'Complex machines that harvest various types of grain crops'
        ];

        $selectedIndustry = $this->faker->unique()->randomElement($industries);

        return [
            'name' => $selectedIndustry,
            'description' => $descriptions[$selectedIndustry],
        ];
    }
}
