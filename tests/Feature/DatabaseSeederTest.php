<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\App;

beforeEach(function (){
   $this->fakeUsersCount = 10;
   $this->fakePostsCount = 200;
   $this->fakeCommentsCount = 4000;

   $this->testUserPosts = 50;
   $this->testUserComments = 100;
});

it('adds given users', function () {
    $this->assertDatabaseCount(User::class, 0);

    $this->artisan('db:seed');

    $this->assertDatabaseCount(User::class, $this->fakeUsersCount);
});

it('adds given posts', function () {
    $this->assertDatabaseCount(Post::class, 0);

    $this->artisan('db:seed');

    $this->assertDatabaseCount(Post::class, $this->fakePostsCount);
});

it('adds given comments', function () {
    $this->assertDatabaseCount(Comment::class, 0);

    $this->artisan('db:seed');

    $this->assertDatabaseCount(Comment::class, $this->fakeCommentsCount);
});

it('adds local test user with posts and comments', function () {
    App::partialMock()->shouldReceive('environment')->andReturn('local');
    $this->assertDatabaseCount(User::class, 0);
    $this->assertDatabaseCount(Post::class, 0);
    $this->assertDatabaseCount(Comment::class, 0);

    $this->artisan('db:seed');

    $this->assertDatabaseCount(User::class, $this->fakeUsersCount + 1);
    $this->assertDatabaseCount(Post::class, $this->fakePostsCount + $this->testUserPosts);
    $this->assertDatabaseCount(Comment::class, $this->fakeCommentsCount + $this->testUserComments);
});

it('does not add test user for production', function () {
    App::partialMock()->shouldReceive('environment')->andReturn('production');
    $this->assertDatabaseCount(User::class, 0);

    $this->artisan('db:seed');

    $this->assertDatabaseCount(User::class, $this->fakeUsersCount);
});
