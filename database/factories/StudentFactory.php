<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['male', 'female', 'other']);

        return [
            'first_name' => $this->faker->firstName($gender === 'male' ? 'male' : ($gender === 'female' ? 'female' : null)),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'gender' => $gender,
            'address' => $this->faker->address(),
            'date_of_birth' => $this->faker->dateTimeBetween('-40 years', '-18 years')->format('Y-m-d'),
            'user_id' => null, // Will be set if withUser() is called
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }

    /**
     * Configure the factory to create a student with a user account.
     */
    public function withUser(?User $user = null): static
    {
        return $this->state(function (array $attributes) use ($user) {
            if ($user) {
                return ['user_id' => $user->id];
            }

            // Create a new user if none provided
            $user = User::factory()->create([
                'name' => $attributes['first_name'] . ' ' . $attributes['last_name'],
                'email' => $attributes['email'],
            ]);

            return ['user_id' => $user->id];
        });
    }

    /**
     * Configure the factory to create an inactive student.
     */
    public function inactive(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }

    /**
     * Configure the factory to create a student with a specific gender.
     */
    public function gender(string $gender): static
    {
        return $this->state(function (array $attributes) use ($gender) {
            return [
                'gender' => $gender,
                'first_name' => $this->faker->firstName($gender === 'male' ? 'male' : ($gender === 'female' ? 'female' : null)),
            ];
        });
    }
}
