<?php

namespace Database\Factories;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Enrollment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'user_id' => User::factory(),
            'class' => $this->faker->randomElement(['A', 'B', 'C']),
            'has_paid' => $this->faker->boolean(70), // 70% chance of having paid
            'enrolled_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'notes' => $this->faker->optional(0.7)->paragraph(), // 70% chance of having notes
        ];
    }

    /**
     * Configure the factory to create an enrollment with payment completed.
     */
    public function paid(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'has_paid' => true,
            ];
        });
    }

    /**
     * Configure the factory to create an enrollment with payment pending.
     */
    public function unpaid(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'has_paid' => false,
            ];
        });
    }

    /**
     * Configure the factory to create an enrollment for a specific class.
     */
    public function inClass(string $class): static
    {
        return $this->state(function (array $attributes) use ($class) {
            return [
                'class' => $class,
            ];
        });
    }

    /**
     * Configure the factory to create an enrollment with specific notes.
     */
    public function withNotes(string $notes): static
    {
        return $this->state(function (array $attributes) use ($notes) {
            return [
                'notes' => $notes,
            ];
        });
    }

    /**
     * Configure the factory to create a recent enrollment.
     */
    public function recent(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'enrolled_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            ];
        });
    }
}
