<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $users = User::factory(10)->create();
         $posts = Post::factory(200)
             ->recycle($users)
             ->has(Comment::factory(20)->recycle($users))
             ->create();
//         $comments = Comment::factory(200)
//             ->recycle($users)
//             ->recycle($posts)
//             ->create();
         $testUser = User::factory()
             ->has(Post::factory(45))
             ->has(Comment::factory(100)->recycle($posts))
             ->create([
             'name' => 'Test User',
             'email' => 'test@example.com',
         ]);
    }
}
