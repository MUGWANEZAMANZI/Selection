<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Transaction;
use App\Models\Examination;
use App\Models\Training;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user if it doesn't exist
        if (!User::where('email', 'mmaudace@trusterlabs.com')->exists()) {
            User::factory()->create([
                'name' => 'MUGABO Kevin',
                'email' => 'mmaudace@trusterlabs.com',
            ]);
        }

        // Create additional users
        User::factory(10)->create();

        // Call the TrainingSeeder to create predefined trainings
        $this->call([
            TrainingSeeder::class,
        ]);

        // Create additional trainings
        $cybersecurityTrainings = Training::factory(3)->cybersecurity()->create();
        $programmingTrainings = Training::factory(3)->programming()->create();
        $networkingTrainings = Training::factory(3)->networking()->create();

        // Get all trainings including those from TrainingSeeder
        $allTrainings = Training::all();

        // Create students
        $students = Student::factory(15)->create();

        // Create students with user accounts
        $studentsWithUsers = Student::factory(5)->withUser()->create();

        $allStudents = $students->merge($studentsWithUsers);

        // Create enrollments
        foreach ($allStudents as $student) {
            // Each student enrolls in 1-3 trainings
            $enrollmentCount = rand(1, 3);
            $trainingsForStudent = $allTrainings->random(min($enrollmentCount, $allTrainings->count()));

            foreach ($trainingsForStudent as $training) {
                $user = User::inRandomOrder()->first();

                Enrollment::factory()->create([
                    'student_id' => $student->id,
                    'user_id' => $user->id,
                    'class' => $this->getRandomClass(),
                    'has_paid' => $this->getRandomBoolean(70), // 70% chance of having paid
                ]);

                // If enrollment is paid, create a successful transaction
                if (rand(1, 100) <= 70) {
                    Transaction::factory()->successful()->create([
                        'student_id' => $student->id,
                        'user_id' => $user->id,
                        'training_id' => $training->id,
                        'amount' => $training->fees,
                        'phone' => $this->getRandomPhone(),
                    ]);
                } else {
                    // Create a pending or failed transaction
                    $status = rand(1, 100) <= 50 ? 'pending' : 'failed';

                    Transaction::factory()->create([
                        'student_id' => $student->id,
                        'user_id' => $user->id,
                        'training_id' => $training->id,
                        'amount' => $training->fees,
                        'status' => $status,
                        'phone' => $this->getRandomPhone(),
                    ]);
                }
            }
        }

        // Create additional transactions
        Transaction::factory(5)->create();

        // Create examinations if they don't exist
        if (!Examination::where('slug', 'ccna-networking-certification-exam')->exists()) {
            Examination::factory()->ccna()->create();
        }

        if (!Examination::where('slug', 'shodan-search-engine-proficiency-test')->exists()) {
            Examination::factory()->shodan()->create();
        }

        if (!Examination::where('slug', 'google-dorking-techniques-assessment')->exists()) {
            Examination::factory()->googleDork()->create();
        }

        // Create additional examinations with unique titles
        for ($i = 1; $i <= 2; $i++) {
            $title = "Custom Examination " . $i;
            Examination::factory()->create([
                'title' => $title,
                'slug' => Str::slug($title),
            ]);
        }
    }

    /**
     * Get a random class (A, B, or C).
     */
    private function getRandomClass(): string
    {
        return ['A', 'B', 'C'][rand(0, 2)];
    }

    /**
     * Get a random boolean with a specified chance of being true.
     */
    private function getRandomBoolean(int $trueChance = 50): bool
    {
        return rand(1, 100) <= $trueChance;
    }

    /**
     * Get a random phone number.
     */
    private function getRandomPhone(): string
    {
        return '078' . rand(1000000, 9999999);
    }
}
