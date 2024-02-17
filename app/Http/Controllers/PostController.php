<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Image;
use App\Models\Post;
use App\Models\TemporaryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Posts/Index', [
            'posts' => PostResource::collection(
                Post::query()
                    ->when(request()->input('search'), function ($query, $search){
                        $query->where('title', 'like', "%{$search}%");
                    })
                    ->latest('id')
                    ->paginate(12))
                    ->withQueryString(),
            'filters' => request()->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Posts/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $post = Post::create([
            ...$request->validate([
                'title' => ['required', 'string', 'max:120', 'min:10'],
                'body' => ['required', 'string', 'max:10000', 'min:100'],
            ]),
            'user_id' => $request->user()->id
        ]);

        if($request->has('images')){
            foreach ($request->images as $tempImage){
                $tempImage = TemporaryImage::where('name', $tempImage)->first();
                Storage::disk('public')->move('images/temp/' . $tempImage->name, 'images/posts/' . $tempImage->name);
                $postImage = Image::create([
                    'user_id' => $request->user()->id,
                    'imageable_type' => Post::class,
                    'imageable_id' => $post->id,
                    'name' => $tempImage->name,
                    'extension' => $tempImage->extension,
                    'size' => $tempImage->size,
                ]);
                $tempImage->delete();

                //solve 'can upload and store a comment with images' bug
                if(TemporaryImage::where('name', $tempImage)->first()){
                    TemporaryImage::where('name', $tempImage)->delete();
                }
            }
        }

        return redirect()->route('posts.show', $post)
            ->banner('Post Published.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load(['user', 'comments', 'images']);
        return inertia('Posts/Show', [
            'post' => fn () => PostResource::make($post),
            'comments' => fn () => CommentResource::collection($post->comments()->with(['user', 'images'])->latest()->latest('id')->paginate(10)),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
