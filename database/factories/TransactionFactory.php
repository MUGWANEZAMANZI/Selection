<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\Training;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_id' => 'TRX-' . Str::upper(Str::random(10)),
            'phone' => $this->faker->phoneNumber(),
            'amount' => $this->faker->numberBetween(50000, 500000),
            'status' => $this->faker->randomElement(['pending', 'successful', 'failed']),
            'training_id' => Training::factory(),
            'user_id' => User::factory(),
            'student_id' => Student::factory(),
            'transaction_time' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Configure the factory to create a successful transaction.
     */
    public function successful(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'successful',
            ];
        });
    }

    /**
     * Configure the factory to create a pending transaction.
     */
    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    /**
     * Configure the factory to create a failed transaction.
     */
    public function failed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'failed',
            ];
        });
    }

    /**
     * Configure the factory to create a recent transaction.
     */
    public function recent(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'transaction_time' => $this->faker->dateTimeBetween('-1 month', 'now'),
            ];
        });
    }

    /**
     * Configure the factory to create a transaction for a specific amount.
     */
    public function forAmount(float $amount): static
    {
        return $this->state(function (array $attributes) use ($amount) {
            return [
                'amount' => $amount,
            ];
        });
    }

    /**
     * Configure the factory to create a transaction for a specific training.
     */
    public function forTraining(Training $training): static
    {
        return $this->state(function (array $attributes) use ($training) {
            return [
                'training_id' => $training->id,
                'amount' => $training->fees,
            ];
        });
    }
}
