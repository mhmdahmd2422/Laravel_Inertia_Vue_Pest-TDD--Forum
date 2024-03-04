<?php

use App\Models\Post;
use App\Models\User;
use App\Notifications\Activity;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

BeforeEach(function (){
    Event::fake();
});

it('sends a notification on new comment to post author', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    Notification::fake();
    Notification::assertNothingSent();

    actingAs($user)->post(route('posts.comments.store', $post), [
        'body' => 'this is a test comment.'
    ]);

    Notification::assertSentTo(
        [$post->user],
        Activity::class
    );

    Notification::assertNotSentTo(
        [$user], Activity::class
    );
});

it('can mark a notification as read', function (){
    $user = User::factory()->create();

    $user->notify(new Activity('message', 'link.com', '/profile_photo'));

    expect($user->unreadNotifications)->toHaveCount(1);

    actingAs($user)->post(route('markNotificationAsRead', $user->unreadNotifications->first()));

    expect($user->refresh()->unreadNotifications->first())->toBe(null);
});

it('can mark all notifications as read', function (){
    $user = User::factory()->create();

    $user->notify(new Activity('message', 'link.com', '/profile_photo'));
    $user->notify(new Activity('message', 'link.com', '/profile_photo'));
    $user->notify(new Activity('message', 'link.com', '/profile_photo'));

    expect($user->unreadNotifications)->toHaveCount(3);

    actingAs($user)->post(route('markAllNotificationsAsRead', $user));

    expect($user->refresh()->unreadNotifications)->each->toBe(null);
});
