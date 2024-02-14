<?php

use App\Models\Post;
use App\Models\User;
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

it('redirects to the post show page with toast', function () {
    actingAs(User::factory()->create())->post(route('posts.store'), $this->validData)
        ->assertRedirectToRoute('posts.show', Post::latest('id')->first())
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
