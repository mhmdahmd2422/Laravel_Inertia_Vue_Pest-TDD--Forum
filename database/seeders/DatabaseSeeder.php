<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $users = User::factory(10)->create();
         $posts = Post::factory(200)
             ->withFixture()
             ->recycle($users)
             ->has(Comment::factory(20)->recycle($users))
             ->create();

        if (App::environment() === 'local') {
            User::factory()
                ->has(Post::factory(50)->withFixture())
                ->has(Comment::factory(50)->recycle($posts))
                ->create([
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ]);
        }
    }
}
