<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['Company', 'Individual']);
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $companyName = $this->faker->company();

        return [
            'uuid' => $this->faker->uuid(),
            'company_id' => Company::factory(),
            'branch_id' => Branch::factory(),
            'client_type' => $type,
            
            'company_name' => $type === 'Company' ? $companyName : null,
            'first_name' => $type === 'Individual' ? $firstName : null,
            'last_name' => $type === 'Individual' ? $lastName : null,
            'display_name' => $type === 'Company' ? $companyName : trim("$firstName $lastName"),
            
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'alternate_phone' => $this->faker->optional()->phoneNumber(),
            'website' => $type === 'Company' ? $this->faker->url() : null,
            
            'tax_number' => $type === 'Company' ? $this->faker->numerify('TAX-########') : null,
            'registration_number' => $type === 'Company' ? $this->faker->numerify('REG-########') : null,
            
            'country' => $this->faker->country(),
            'state' => $this->faker->state(),
            'city' => $this->faker->city(),
            'address' => $this->faker->streetAddress(),
            'postal_code' => $this->faker->postcode(),
            
            'logo' => null,
            'status' => 'active',
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
