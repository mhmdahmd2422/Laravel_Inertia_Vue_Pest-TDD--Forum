<?php

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

it('can login with facebook api', function (){
    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('redirect')->andReturn('Redirected');

    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');

    $abstractUser
        ->shouldReceive('getId')->andReturn($providerId = '12345654321345')
        ->shouldReceive('getEmail')
        ->andReturn($email = Str::random(10).'@noemail.app')
        ->shouldReceive('getname')
        ->andReturn($name = 'Laztopaz')
        ->shouldReceive('getNickname')
        ->andReturn('Laztopaz')
        ->shouldReceive('getAvatar')
        ->andReturn($avatarUrl = 'https://en.gravatar.com/userimage');

    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with('facebook')->andReturn($provider);

    $this->get(route('callback.facebook'))
        ->assertRedirectToRoute('dashboard');

    expect(auth()->check())->toBeTrue()
        ->and(User::first())
        ->profile_photo_path->toBe($avatarUrl)
        ->provider_id->toBe($providerId)
        ->name->toBe($name)
        ->email->toBe($email);

});

it('redirects to the correct Google sign in url', function () {
    $driver = Mockery::mock('Laravel\Socialite\Two\FacebookProvider');
    $driver->shouldReceive('redirect')
        ->andReturn(new RedirectResponse(route('login.facebook')));

    Socialite::shouldReceive('driver')->andReturn($driver);

    $this->get(route('login.facebook'))
        ->assertRedirect(route('login.facebook'));
});

