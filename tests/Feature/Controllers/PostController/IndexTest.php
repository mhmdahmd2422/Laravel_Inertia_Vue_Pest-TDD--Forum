<?php

use App\Http\Resources\PostResource;
use App\Models\Post;
use function Pest\Laravel\get;

it('should return the correct component', function (){
    get(route('posts.index'))
        ->assertOk()
        ->assertComponent('Posts/Index');
});

it('passes posts to the view', function () {
    $posts = Post::factory(12)->create();
    get(route('posts.index'))
        ->assertOk()
            ->assertHasPaginatedResource('posts', PostResource::collection($posts->reverse()));
});

it('passes only search filtered posts to the view', function () {
    $posts = Post::factory(20)->create();
    get(route('posts.index',['search' => 'que']))
        ->assertOk()
        ->assertHasPaginatedResource('posts', PostResource::collection($posts->filter(function ($item) {
            return preg_match('/que/', $item->title)? $item: false;
        })->reverse())->withQuery(['search' => 'que']));
});

