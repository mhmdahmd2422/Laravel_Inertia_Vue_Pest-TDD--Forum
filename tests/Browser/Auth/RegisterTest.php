<?php

namespace Tests\Browser\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegisterTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_email_register(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visitRoute('register')
                ->type('#name', 'Tester')
                ->type('#email', 'tester@example.com')
                ->type('#password', 'password')
                ->type('#password_confirmation', 'password')
                ->click('button[type="submit"]')
                ->pause(1000)
                ->waitForRoute('posts.index')
                ->assertAuthenticated();
        });
    }
}
