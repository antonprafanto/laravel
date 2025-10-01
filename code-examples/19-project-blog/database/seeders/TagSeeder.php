<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Laravel',
            'PHP',
            'JavaScript',
            'Vue.js',
            'React',
            'Node.js',
            'Python',
            'Django',
            'Docker',
            'AWS',
            'Git',
            'MySQL',
            'PostgreSQL',
            'MongoDB',
            'Redis',
            'API',
            'REST',
            'GraphQL',
            'Testing',
            'Security',
        ];

        foreach ($tags as $tag) {
            Tag::create([
                'name' => $tag,
                'slug' => Str::slug($tag),
            ]);
        }
    }
}
