<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $users = User::factory(10)->create();
        $users->each(function ($user) {
            $categories =  Category::factory(rand(0, 10))->create(['user_id' => $user->id]);
            $categories->each(function ($category) use ($user) {
                Post::factory(rand(0, 50))
                    ->create(['user_id' => $user->id, 'category_id' => $category->id]);
            });
        });
    }
}
