<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 posts
        Post::factory(50)->create()->each(function ($post) {
            // Attach random 2-4 tags to each post
            $post->tags()->attach(
                Tag::inRandomOrder()->limit(rand(2, 4))->pluck('id')
            );
        });
    }
}
