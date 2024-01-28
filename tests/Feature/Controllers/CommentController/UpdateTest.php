<?php

use App\Models\Comment;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;

it('requires authentication', function (){
    put(route('comments.update', Comment::factory()->create()))
        ->assertRedirectToRoute('login');
});

it('can update a comment', function () {
    $comment = Comment::factory()->create(['body' => 'This is an old body']);
    $newBody = 'This is a new body';

    actingAs($comment->user)
    ->put(route('comments.update', $comment), ['body' => $newBody]);

    $this->assertDatabaseHas(Comment::class, [
        'id' => $comment->id,
        'body' => $newBody,
    ]);
});

it('redirects to the post page', function () {
    $comment = Comment::factory()->create();

    actingAs($comment->user)
        ->put(route('comments.update', $comment), ['body' => 'This is a new body'])
        ->assertRedirectToRoute('posts.show', $comment->post);
});

it('redirects to the correct comments page', function () {
    $comment = Comment::factory()->create();

    actingAs($comment->user)
        ->put(route('comments.update', ['comment' => $comment, 'page' => 2]), ['body' => 'This is a new body'])
        ->assertRedirectToRoute('posts.show', ['post' => $comment->post, 'page' => 2]);
});

it('cannot update a comment from another user', function () {
    $comment = Comment::factory()->create();

    actingAs(User::factory()->create())
        ->put(route('comments.update', ['comment' => $comment]), ['body' => 'This is a new body'])
        ->assertForbidden();
});

it('requires a valid body', function ($testValue) {
    $comment = Comment::factory()->create();

    actingAs($comment->user)
        ->put(route('comments.update', ['comment' => $comment]),
            [
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
