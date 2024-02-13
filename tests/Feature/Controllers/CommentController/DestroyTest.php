<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;

it('requires authentication', function () {
    delete(route('comments.destroy', Comment::factory()->create()))
        ->assertRedirectToRoute('login');
});

it('can delete a comment', function () {
    $comment = Comment::factory()->create();
    actingAs($comment->user)->delete(route('comments.destroy', $comment));
    $this->assertModelMissing($comment);
});

it('can delete a comment with images', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $images = [];
    for ($i=0;$i<3;$i++){
        $images[] = \Illuminate\Http\UploadedFile::fake()->image('commentImage-'.$i.'.png');
    }
    foreach ($images as $image) {
        actingAs($user)->post(url('/upload'), [
            'image' => $image
        ]);
    }
    actingAs($user)->post(route('posts.comments.store', $post), [
        'body' => 'This is a test comment.',
        'images' => array_map(function ($image){
            return $image->hashName();
        }, $images),
    ]);
    $comment = $user->comments()->first();
    actingAs($user)->delete(route('comments.destroy', $comment->id));
    foreach ($images as $image) {
        Storage::assertMissing('public/images/comments/' . $image->hashName());
        $this->assertDatabaseMissing(\App\Models\CommentImage::class, [
            'name' => $image->hashName(),
            'extension' => $image->extension(),
            'size' => $image->getSize(),
        ]);
    }
    $this->assertDatabaseMissing(\App\Models\Comment::class, [
        'id' => $comment->id,
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
});

it('redirects to the post show page with toast', function () {
    $comment = Comment::factory()->create();
    actingAs($comment->user)->delete(route('comments.destroy', $comment))
        ->assertRedirectToRoute('posts.show', $comment->post_id)
        ->assertSessionHas('flash.banner', 'Comment Deleted.');
});

it('prevents deleting a comment by another user', function () {
    $comment = Comment::factory()->create();
    actingAs(\App\Models\User::factory()->create())
        ->delete(route('comments.destroy', $comment))
        ->assertForbidden();
    $this->assertModelExists($comment);
});

it('prevents deleting a comment created more than one hour ago', function () {
    $this->freezeTime();
    $comment = Comment::factory()->create();
    $this->travel(1)->hour();
    actingAs($comment->user)
        ->delete(route('comments.destroy', $comment))
        ->assertForbidden();
    $this->assertModelExists($comment);
});

it('redirects to the post show page with the page query parameter', function () {
    $comment = Comment::factory()->create();
    actingAs($comment->user)->delete(route('comments.destroy', ['comment' => $comment, 'page' => 2]))
        ->assertRedirectToRoute('posts.show', ['post' => $comment->post_id, 'page' => 2]);
});
