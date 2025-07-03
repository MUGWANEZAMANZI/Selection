<?php

namespace Database\Factories;

use App\Models\Examination;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Examination>
 */
class ExaminationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Examination::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['ccna', 'shodan', 'google_dork'];
        $category = $this->faker->randomElement($categories);

        $title = match($category) {
            'ccna' => 'CCNA Networking Certification Exam',
            'shodan' => 'Shodan Search Engine Proficiency Test',
            'google_dork' => 'Google Dorking Techniques Assessment',
        };

        $description = match($category) {
            'ccna' => 'Test your knowledge of networking concepts, protocols, and Cisco technologies required for the CCNA certification.',
            'shodan' => 'Evaluate your understanding of Shodan search engine capabilities and techniques for finding internet-connected devices.',
            'google_dork' => 'Assess your skills in advanced Google search techniques for finding specific information on the web.',
        };

        // Set the path to the JSON file
        $jsonFilePath = "seeders/json/{$category}_questions.json";

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $description,
            'json_file_path' => $jsonFilePath,
            'category' => $category,
            'time_limit' => $this->faker->numberBetween(30, 60),
            'passing_score' => $this->faker->numberBetween(70, 80),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
        ];
    }

    /**
     * Configure the factory to create a CCNA examination.
     */
    public function ccna(): static
    {
        return $this->state(function (array $attributes) {
            $title = 'CCNA Networking Certification Exam';
            $jsonFilePath = 'seeders/json/ccna_questions.json';

            return [
                'title' => $title,
                'slug' => Str::slug($title),
                'description' => 'Test your knowledge of networking concepts, protocols, and Cisco technologies required for the CCNA certification.',
                'json_file_path' => $jsonFilePath,
                'category' => 'ccna',
            ];
        });
    }

    /**
     * Configure the factory to create a Shodan examination.
     */
    public function shodan(): static
    {
        return $this->state(function (array $attributes) {
            $title = 'Shodan Search Engine Proficiency Test';
            $jsonPath = database_path('seeders/json/shodan_questions.json');
            $questions = json_decode(file_get_contents($jsonPath), true)['questions'];

            return [
                'title' => $title,
                'slug' => Str::slug($title),
                'description' => 'Evaluate your understanding of Shodan search engine capabilities and techniques for finding internet-connected devices.',
                'json_file_path' => $jsonPath,
                'category' => 'shodan',
            ];
        });
    }

    /**
     * Configure the factory to create a Google Dork examination.
     */
    public function googleDork(): static
    {
        return $this->state(function (array $attributes) {
            $title = 'Google Dorking Techniques Assessment';
            $jsonPath = database_path('seeders/json/google_dork_questions.json');
            $questions = json_decode(file_get_contents($jsonPath), true)['questions'];

            return [
                'title' => $title,
                'slug' => Str::slug($title),
                'description' => 'Assess your skills in advanced Google search techniques for finding specific information on the web.',
                'json_file_path' => $jsonPath,
                'category' => 'google_dork',
            ];
        });
    }
}
