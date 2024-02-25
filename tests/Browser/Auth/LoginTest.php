<?php

namespace Tests\Browser\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_email_login(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->visitRoute('login')
                ->type('#email', $user->email)
                ->type('#password', 'password')
                ->click('button[type="submit"]')
                ->assertAuthenticated()
                ->assertRouteIs('posts.index');
        });
    }

    public function test_facebook_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visitRoute('login')
                ->clickLink('Sign in with Facebook')
                ->assertUrlIs('https://www.facebook.com/login.php');
        });
    }

    public function test_twitter_login(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visitRoute('login')
                ->clickLink('Sign in with Twitter')
                ->assertPathBeginsWith('/oauth/authenticate');
        });
    }
}
