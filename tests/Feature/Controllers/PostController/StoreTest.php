<?php

use App\Models\Image;
use App\Models\Post;
use App\Models\TemporaryImage;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

beforeEach(function (){
   $this->validData = [
       'title' => 'test post title',
       'body' => str_repeat('test post body ', 7)
   ];
});

it('requires authentication', function (){
   post(route('posts.store'))
       ->assertRedirectToRoute('login');
});

it('stores a post', function () {
    $user = User::factory()->create();

    actingAs($user)->post(route('posts.store'), $this->validData);

    $this->assertDatabaseHas(Post::class, [
       'user_id' => $user->id,
        ...$this->validData,
    ]);
});

it('can upload and store a post with images', function (){
    $user = User::factory()->create();
    $images = uploadFakeImages($user, 3);
    foreach ($images as $image) {
        Storage::assertExists('public/images/temp/' . $image->hashName());
        $this->assertDatabaseHas(TemporaryImage::class, [
            'name' => $image->hashName(),
            'extension' => $image->extension(),
            'size' => $image->getSize(),
        ]);
    }
    actingAs($user)->post(route('posts.store'), [
        ...$this->validData,
        'images' => array_map(function ($image){
            return $image->hashName();
        }, $images),
    ]);
    foreach ($images as $image) {
        Storage::assertExists('public/images/posts/' . $image->hashName());
        Storage::assertMissing('public/images/temp/' . $image->hashName());
        $this->assertDatabaseMissing(TemporaryImage::class, [
            'name' => $image->hashName(),
            'extension' => $image->extension(),
            'size' => $image->getSize(),
        ]);
        $this->assertDatabaseHas(Image::class, [
            'user_id' => $user->id,
            'imageable_type' => Post::class,
            'name' => $image->hashName(),
            'extension' => $image->extension(),
            'size' => $image->getSize(),
        ]);
    }

    expect(Post::first()->images)
        ->toHaveCount(3);

    //clean-up
    clearImages('posts', Post::first()->images);
});

it('redirects to the post show page with toast', function () {
    actingAs(User::factory()->create())->post(route('posts.store'), $this->validData)
        ->assertRedirect(Post::latest('id')->first()->showRoute())
        ->assertSessionHas('flash.banner', 'Post Published.');;
});

it('requires a valid data', function (array $badData, array|string $errors) {
    $user = User::factory()->create();

    actingAs($user)->post(route('posts.store'), [...$this->validData, ...$badData])
        ->assertInvalid($errors);
})->with([
    [['title' => null], 'title'],
    [['title' => 2], 'title'],
    [['title' => 1.5], 'title'],
    [['title' => true], 'title'],
    [['title' => str_repeat('a', 121)], 'title'],
    [['title' => str_repeat('a', 9)], 'title'],
    [['body' => null], 'body'],
    [['body' => 2], 'body'],
    [['body' => 1.5], 'body'],
    [['body' => true], 'body'],
    [['body' => str_repeat('a', 10_001)], 'body'],
    [['body' => str_repeat('a', 99)], 'body'],
]);
