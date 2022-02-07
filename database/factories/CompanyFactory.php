<?php

namespace Database\Factories;

use App\Models\Company;
use App\Traits\Generate;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    use Generate;
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => Generate::uuid4(),
            'email' => "info@pretigesystem.com",
            'name' => "Prestige Systems & Data Solutions",
        ];
    }
}
