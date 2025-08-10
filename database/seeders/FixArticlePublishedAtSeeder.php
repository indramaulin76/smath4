<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FixArticlePublishedAtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update all articles with null published_at to use created_at
        \App\Models\Article::whereNull('published_at')
            ->update([
                'published_at' => \DB::raw('created_at'),
                'is_published' => true
            ]);

        $this->command->info('Articles published_at field updated successfully.');
    }
}
