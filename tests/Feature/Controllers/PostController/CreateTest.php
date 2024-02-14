<?php

use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function (){
   get(route('posts.create'))
       ->assertRedirectToRoute('login');
});

it('returns the correct component', function () {
    actingAs(User::factory()->create())
        ->get(route('posts.create'))
        ->assertOk()
        ->assertComponent('Posts/Create');
});
