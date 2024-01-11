<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\post;

it('requires authentication', function () {
    post(route('posts.comments.store', Post::factory()->create()))
        ->assertRedirectToRoute('login');
});

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

it('can upload and store a comment with images', function (){
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $images = [];
    for ($i=0;$i<3;$i++){
        $images[] = \Illuminate\Http\UploadedFile::fake()->image('commentImage-'.$i.'.png');
    }
    foreach ($images as $image) {
        \Pest\Laravel\actingAs($user)->post(url('/upload'), [
            'image' => $image
        ]);
        Storage::assertExists('public/images/temp/' . $image->hashName());
        $this->assertDatabaseHas(\App\Models\TemporaryImage::class, [
            'name' => $image->hashName(),
            'extension' => $image->extension(),
            'size' => $image->getSize(),
        ]);
    }
    \Pest\Laravel\actingAs($user)->post(route('posts.comments.store', $post), [
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
        $this->assertDatabaseMissing(\App\Models\TemporaryImage::class, [
            'name' => $image->hashName(),
            'extension' => $image->extension(),
            'size' => $image->getSize(),
        ]);
        $this->assertDatabaseHas(\App\Models\CommentImage::class, [
            'name' => $image->hashName(),
            'extension' => $image->extension(),
            'size' => $image->getSize(),
        ]);
    }
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
