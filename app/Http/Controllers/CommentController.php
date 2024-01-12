<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentImage;
use App\Models\Post;
use App\Models\TemporaryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Post $post)
    {
        $comment = Comment::create([
            ...$request->validate([
                'body' => ['required', 'string', 'max:2500'],
            ]),
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
        ]);

        if($request->has('images')){
            foreach ($request->images as $tempImage){
                $tempImage = TemporaryImage::where('name', $tempImage)->first();
                Storage::disk('public')->move('images/temp/'. $tempImage->name, 'images/comments/'. $tempImage->name);
                CommentImage::create([
                    'user_id' => $request->user()->id,
                    'comment_id' => $comment->id,
                    'name' => $tempImage->name,
                    'extension' => $tempImage->extension,
                    'size' => $tempImage->size,
                ]);
                $tempImage->delete();
            }
        }

        return redirect()->route('posts.show', $post);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Comment $comment)
    {
        $this->authorize('delete', $comment);

        if($comment->images){
            foreach ($comment->images as $commentImage){
                 Storage::disk('public')->delete('images/comments/'. $commentImage->name);
                $commentImage->delete();
            }
        }
        $comment->delete();

        return redirect()->route('posts.show', $comment->post_id);
    }
}
