<?php

namespace Tests\Browser\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LogoutTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_logout(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::factory()->create())
                ->visitRoute('dashboard')
                ->press('#account-dropdown')
                ->press('Log Out')
                ->waitForRoute('home')
                ->assertGuest();
        });
    }
}
