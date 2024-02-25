<?php

namespace Tests\Browser;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PostTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_can_view_index_of_posts(): void
    {
        $post = Post::factory()->create();

        $this->browse(function (Browser $browser) use ($post) {
            $browser
                ->visitRoute('posts.index')
                ->assertSee($post->title);
        });
    }

    public function test_can_view_a_post(): void
    {
        $post = Post::factory()->create(['body' => 'test body']);

        $this->browse(function (Browser $browser) use ($post) {
            $browser
                ->visitRoute('posts.show', [$post, Str::slug($post->title)])
                ->assertSee($post->title)
                ->assertSee($post->body)
                ->assertSee($post->user->name);
        });
    }

    public function test_can_create_a_post(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::factory()->create())
                ->visitRoute('posts.index')
                ->clickAtXPath("//body/div[@id='app']/div[1]/div[2]/main[1]/div[1]/button[1]")
                ->waitForRoute('posts.create')
                ->type('#title', 'test title')
                ->type('#body', str_repeat('test post ', 30))
                ->clickAtXPath("//body/div[@id='app']/div[1]/div[2]/main[1]/div[1]/li[1]/form[1]/div[4]/button[1]")
                ->pause(1000)
                ->waitForRoute('posts.show', [Post::first(), Str::slug(Post::first()->title)])
                ->assertSee('Post Published');
        });}
}
