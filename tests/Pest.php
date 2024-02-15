<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\actingAs;

uses(
    Tests\TestCase::class,
     LazilyRefreshDatabase::class,
)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function createCommentWithImages(User $user, Post $post, int $imageCount): array
{
    $images = uploadFakeImages($user, $imageCount);

    actingAs($user)->post(route('posts.comments.store', $post), [
        'body' => 'this is a test comment.',
        'images' => array_map(function ($image){
            return $image->hashName();
        }, $images),
    ]);

    return $images;
}

function uploadFakeImages(User $user, int $imageCount): array
{
    $images = [];
    for ($i=0;$i<$imageCount;$i++){
        $images[] = UploadedFile::fake()->image('commentImage-'.$i.'.png');
    }

    foreach ($images as $image) {
        actingAs($user)->post(url('/upload'), [
            'image' => $image
        ]);
    }

    return $images;
}

function clearImages(string $directory, Collection $images)
{
    foreach ($images as $image){
        Storage::disk('public')->delete('images/'.$directory.'/'. $image->name);
        $image->delete();
    }
}
