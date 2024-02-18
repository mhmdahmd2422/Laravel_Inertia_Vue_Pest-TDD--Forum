<?php

use App\Models\Comment;
use App\Models\Image;
use App\Models\Post;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;

it('requires authentication', function (){
    put(route('comments.update', Comment::factory()->create()))
        ->assertRedirectToRoute('login');
});

it('can update a comment body', function () {
    $comment = Comment::factory()->create(['body' => 'This is an old body']);
    $newBody = 'This is a new body';

    actingAs($comment->user)
    ->put(route('comments.update', $comment), ['body' => $newBody]);

    $this->assertDatabaseHas(Comment::class, [
        'id' => $comment->id,
        'body' => $newBody,
    ]);
});

it('can add photos to existing comment that includes photos', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    createCommentWithImages($user, $post, 2);
    $comment = Comment::first();

    expect($comment->images)
        ->tohaveCount(2);

    actingAs($comment->user)
        ->put(route('comments.update', $comment), [
            'body' => 'This is a new body',
            'images' => array_map(function ($image){
                return $image->hashName();
            },
            $images = uploadFakeImages($user, 2)),
        ]);
    foreach ($images as $image) {
        $this->assertDatabaseHas(Image::class, [
            'user_id' => $user->id,
            'imageable_type'  => Comment::class,
            'imageable_id'  => $comment->id,
            'name' => $image->hashName(),
        ]);
    }
    expect($comment->fresh()->images)
        ->tohaveCount(4);

    //clean-up
    clearImages('comments', $comment->fresh()->images);
});

it('redirects to the post page', function () {
    $comment = Comment::factory()->create();

    actingAs($comment->user)
        ->put(route('comments.update', $comment), ['body' => 'This is a new body'])
        ->assertRedirect($comment->post->showRoute())
        ->assertSessionHas('flash.banner', 'Comment Updated.');
});

it('redirects to the correct comments page', function () {
    $comment = Comment::factory()->create();

    actingAs($comment->user)
        ->put(route('comments.update', ['comment' => $comment, 'page' => 2]), ['body' => 'This is a new body'])
        ->assertRedirect($comment->post->showRoute(['page' => 2]));
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
