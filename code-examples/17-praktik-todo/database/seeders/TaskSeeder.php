<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            [
                'title' => 'Belajar Laravel Routing',
                'description' => 'Pelajari cara membuat routes di Laravel',
                'is_completed' => true,
            ],
            [
                'title' => 'Belajar Eloquent ORM',
                'description' => 'Pahami cara kerja Model dan database query',
                'is_completed' => true,
            ],
            [
                'title' => 'Buat To-Do List App',
                'description' => 'Implementasi CRUD lengkap untuk To-Do List',
                'is_completed' => false,
            ],
            [
                'title' => 'Belajar Blade Template',
                'description' => 'Master Blade templating engine',
                'is_completed' => false,
            ],
            [
                'title' => 'Belajar Authentication',
                'description' => 'Setup Laravel Breeze untuk login/register',
                'is_completed' => false,
            ],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
