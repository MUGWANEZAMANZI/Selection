<?php

namespace Database\Factories;

use App\Models\Training;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Training>
 */
class TrainingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Training::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $classes = [
            'Cybersecurity' => [
                'Network Security',
                'Ethical Hacking',
                'Security Operations',
                'Digital Forensics',
                'Penetration Testing'
            ],
            'Programming' => [
                'Web Development',
                'Mobile App Development',
                'Python Programming',
                'Java Programming',
                'Full Stack Development'
            ],
            'Networking' => [
                'CCNA Preparation',
                'Network Administration',
                'Cloud Networking',
                'Network Design',
                'Wireless Networking'
            ],
            'Data Science' => [
                'Data Analysis',
                'Machine Learning',
                'Big Data',
                'Data Visualization',
                'Statistical Analysis'
            ]
        ];

        $class = $this->faker->randomElement(array_keys($classes));
        $name = $this->faker->randomElement($classes[$class]);

        $durations = ['2 weeks', '1 month', '2 months', '3 months', '6 months'];

        return [
            'name' => $name,
            'fees' => $this->faker->numberBetween(50000, 500000), // Fees in RWF
            'duration' => $this->faker->randomElement($durations),
            'description' => $this->faker->paragraph(3),
            'class' => $class,
        ];
    }

    /**
     * Configure the factory to create a Cybersecurity training.
     */
    public function cybersecurity(): static
    {
        return $this->state(function (array $attributes) {
            $names = [
                'Network Security',
                'Ethical Hacking',
                'Security Operations',
                'Digital Forensics',
                'Penetration Testing'
            ];

            return [
                'name' => $this->faker->randomElement($names),
                'class' => 'Cybersecurity',
                'description' => $this->faker->paragraph(3) . ' This course focuses on cybersecurity principles and practices.',
            ];
        });
    }

    /**
     * Configure the factory to create a Programming training.
     */
    public function programming(): static
    {
        return $this->state(function (array $attributes) {
            $names = [
                'Web Development',
                'Mobile App Development',
                'Python Programming',
                'Java Programming',
                'Full Stack Development'
            ];

            return [
                'name' => $this->faker->randomElement($names),
                'class' => 'Programming',
                'description' => $this->faker->paragraph(3) . ' This course teaches programming skills and software development.',
            ];
        });
    }

    /**
     * Configure the factory to create a Networking training.
     */
    public function networking(): static
    {
        return $this->state(function (array $attributes) {
            $names = [
                'CCNA Preparation',
                'Network Administration',
                'Cloud Networking',
                'Network Design',
                'Wireless Networking'
            ];

            return [
                'name' => $this->faker->randomElement($names),
                'class' => 'Networking',
                'description' => $this->faker->paragraph(3) . ' This course covers networking concepts and technologies.',
            ];
        });
    }
}
