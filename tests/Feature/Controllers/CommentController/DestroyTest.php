<?php

use App\Models\Comment;
use App\Models\Image;
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
    $images = createCommentWithImages($user, $post, 3);
    $comment = $user->comments()->first();
    actingAs($user)->delete(route('comments.destroy', $comment->id));
    foreach ($images as $image) {
        Storage::assertMissing('public/images/comments/' . $image->hashName());
        $this->assertDatabaseMissing(Image::class, [
            'name' => $image->hashName(),
            'imageable_type' => Comment::class,
            'imageable_id' => $comment->id,
            'extension' => $image->extension(),
            'size' => $image->getSize(),
        ]);
    }
    $this->assertModelMissing($comment);
});

it('can delete photos from existing comment that includes photos', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    createCommentWithImages($user, $post, 2);
    $comment = Comment::first();

    $imageToBeDeleted = $comment->images->first();

    actingAs($comment->user)
        ->delete(route('image.destroy', $imageToBeDeleted));

    Storage::assertMissing('public/images/comments/' . $imageToBeDeleted);
    $this->assertDatabaseMissing(Image::class, [
        'user_id' => $user->id,
        'imageable_type'  => Comment::class,
        'imageable_id'  => $comment->id,
        'name' => $imageToBeDeleted->name,
    ]);

    expect($comment->fresh()->images)
        ->toHaveCount(1);

    //clean-up
    clearImages('comments', $comment->images);
});

it('redirects to the post show page with toast', function () {
    $comment = Comment::factory()->create();
    actingAs($comment->user)->delete(route('comments.destroy', $comment))
        ->assertRedirect($comment->post->showRoute())
        ->assertSessionHas('flash.banner', 'Comment Deleted.');
});

it('prevents deleting a comment by another user', function () {
    $comment = Comment::factory()->create();
    actingAs(User::factory()->create())
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
        ->assertRedirect($comment->post->showRoute(['page' => 2]));
});
