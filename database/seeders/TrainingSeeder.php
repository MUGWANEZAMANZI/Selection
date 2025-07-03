<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Training; // Adjust the namespace according to your application structure

class TrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $trainings = [
            [
                'name' => 'Cisco CCNA Networking',
                'fees' => 100,
                'duration' => '3 months',
                'description' => 'Core networking fundamentals and routing.',
                'class' => 'A',
            ],
            [
                'name' => 'Intro to Cyber',
                'fees' => 100,
                'duration' => '2 months',
                'description' => 'Learn the basics of cybersecurity and threat analysis.',
                'class' => 'B',
            ],
            [
                'name' => 'Intro to Linux',
                'fees' => 100,
                'duration' => '6 weeks',
                'description' => 'Linux shell, permissions, and administration.',
                'class' => 'A',
            ],
            [
                'name' => 'IT Essentials',
                'fees' => 100,
                'duration' => '2.5 months',
                'description' => 'Computer hardware, software, and troubleshooting.',
                'class' => 'C',
            ],
            [
                'name' => 'Interview Preparation',
                'fees' => 100,
                'duration' => '1 month',
                'description' => 'Prepare for technical interviews with mock tests.',
                'class' => 'B',
            ],
        ];

        foreach ($trainings as $training) {
            Training::create($training);
        }
    }
}
