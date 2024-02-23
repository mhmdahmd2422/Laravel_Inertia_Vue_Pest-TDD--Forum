<?php

namespace App\Http\Controllers\Socialite;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FacebookLoginController extends Controller
{
    public function redirectToFacebook(): RedirectResponse
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        $facebookUser = Socialite::driver('facebook')->user();

        $user = User::firstOrCreate(
            [
                'provider_id' => $facebookUser->getId(),
            ],
            [
                'email' => $facebookUser->getEmail(),
                'name' => $facebookUser->getName(),
                'profile_photo_path' => $facebookUser->getAvatar(),
            ]
        );

        $user->markEmailAsVerified();

        auth()->login($user, true);

        return redirect()->route('dashboard');
    }
}
