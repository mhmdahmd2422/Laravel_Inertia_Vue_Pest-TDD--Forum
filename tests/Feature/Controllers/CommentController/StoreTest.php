<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

it('can store a comment', function (){
    $user = User::factory()->create();
    $post = Post::factory()->create();

    \Pest\Laravel\actingAs($user)->post(route('posts.comments.store', $post), [
        'body' => 'this is a test comment.'
    ]);

    $this->assertDatabaseHas(Comment::class, [
        'user_id' => $user->id,
        'post_id' => $post->id,
        'body' => 'this is a test comment.'
    ]);

});

it('redirects to the post show page', function () {
    $post = Post::factory()->create();

    \Pest\Laravel\actingAs(User::factory()->create())
        ->post(route('posts.comments.store', $post), [
        'body' => 'this is a test comment.'
    ])
    ->assertRedirectToRoute('posts.show', $post);
});

it('requires a valid body', function ($testValue) {
    $post = Post::factory()->create();

    \Pest\Laravel\actingAs(User::factory()->create())
        ->post(route('posts.comments.store', $post), [
            'body' => $testValue
        ])
        ->assertInvalid('body');
})->with([
    null,
    2,
    1.5,
    true,
    str_repeat('a', 2501),
]);
