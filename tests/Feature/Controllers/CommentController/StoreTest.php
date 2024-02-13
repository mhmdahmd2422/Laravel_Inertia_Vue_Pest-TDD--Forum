<?php

use App\Models\Comment;
use App\Models\CommentImage;
use App\Models\Post;
use App\Models\TemporaryImage;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

it('requires authentication', function () {
    post(route('posts.comments.store', Post::factory()->create()))
        ->assertRedirectToRoute('login');
});

it('can store a comment', function (){
    $user = User::factory()->create();
    $post = Post::factory()->create();

    actingAs($user)->post(route('posts.comments.store', $post), [
        'body' => 'this is a test comment.'
    ]);

    $this->assertDatabaseHas(Comment::class, [
        'user_id' => $user->id,
        'post_id' => $post->id,
        'body' => 'this is a test comment.'
    ]);
});

it('can upload and store a comment with images', function (){
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $images = uploadFakeImages($user, 3);
    foreach ($images as $image) {
        actingAs($user)->post(url('/upload'), [
            'image' => $image
        ]);
        Storage::assertExists('public/images/temp/' . $image->hashName());
        $this->assertDatabaseHas(TemporaryImage::class, [
            'name' => $image->hashName(),
            'extension' => $image->extension(),
            'size' => $image->getSize(),
        ]);
    }
    actingAs($user)->post(route('posts.comments.store', $post), [
        'body' => 'this is a test comment.',
        'images' => array_map(function ($image){
            return $image->hashName();
        }, $images),
    ]);

    $this->assertDatabaseHas(Comment::class, [
        'user_id' => $user->id,
        'post_id' => $post->id,
        'body' => 'this is a test comment.'
    ]);
    foreach ($images as $image) {
        Storage::assertExists('public/images/comments/' . $image->hashName());
        Storage::assertMissing('public/images/temp/' . $image->hashName());
        $this->assertDatabaseMissing(TemporaryImage::class, [
            'name' => $image->hashName(),
            'extension' => $image->extension(),
            'size' => $image->getSize(),
        ]);
        $this->assertDatabaseHas(CommentImage::class, [
            'user_id' => $user->id,
            'name' => $image->hashName(),
            'extension' => $image->extension(),
            'size' => $image->getSize(),
        ]);
    }
});

it('redirects to the post show page with toast', function () {
    $post = Post::factory()->create();

    actingAs(User::factory()->create())
        ->post(route('posts.comments.store', $post), [
        'body' => 'this is a test comment.'
    ])
    ->assertRedirectToRoute('posts.show', $post)
    ->assertSessionHas('flash.banner', 'Comment Added.');
});

it('requires a valid body', function ($testValue) {
    $post = Post::factory()->create();

    actingAs(User::factory()->create())
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
