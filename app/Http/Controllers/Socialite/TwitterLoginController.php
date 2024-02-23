<?php

namespace App\Http\Controllers\Socialite;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TwitterLoginController extends Controller
{
    public function redirectToTwitter(): RedirectResponse
    {
        return Socialite::driver('twitter')->redirect();
    }

    public function handleTwitterCallback()
    {
        $twitterUser = Socialite::driver('twitter')->user();

        $user = User::firstOrCreate(
            [
                'provider_id' => $twitterUser->getId(),
            ],
            [
                'email' => $twitterUser->getEmail(),
                'name' => $twitterUser->getName(),
                'profile_photo_path' => $twitterUser->getAvatar(),
            ]
        );

        $user->markEmailAsVerified();

        auth()->login($user, true);

        return redirect()->route('dashboard');
    }
}
