<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Image;
use App\Models\Post;
use App\Models\TemporaryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Comment::class);
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
                $this->attachImageToComment($tempImage, $request->user()->id, $comment);

                //solve 'can upload and store a comment with images' bug
                if(TemporaryImage::where('name', $tempImage)->first()){
                    TemporaryImage::where('name', $tempImage)->delete();
                }
            }
        }

        return redirect()->route('posts.show', $post)
            ->banner('Comment Added.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        if($request->has('images')){
            $imagesId = [];
            foreach ($request->images as $tempImage){
                if(!is_array($tempImage)){
                    $imagesId[] = $this->attachImageToComment($tempImage, $request->user()->id, $comment);
                }
            }
        }
        $ValidatedData = $request->validate([
            'body' => ['required', 'string', 'max:2500'],
        ]);

        $comment->update($ValidatedData);

        return redirect()->route('posts.show', [
            'post' => $comment->post_id,
            'page' => $request->query('page')
        ])
            ->with(['info' => $imagesId?? null])
            ->banner('Comment Updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Comment $comment)
    {
        if($comment->images){
            foreach ($comment->images as $commentImage){
                 Storage::disk('public')->delete('images/comments/'. $commentImage->name);
                $commentImage->delete();
            }
        }
        $comment->delete();

        return redirect()->route('posts.show', [
            'post' => $comment->post_id,
            'page' => $request->query('page')
        ])->banner('Comment Deleted.');;
    }

    public function attachImageToComment(string $tempImage, int $userId, Comment $comment)
    {
        $tempImage = TemporaryImage::where('name', $tempImage)->first();
        Storage::disk('public')->move('images/temp/' . $tempImage->name, 'images/comments/' . $tempImage->name);
        $commentImage = Image::create([
            'user_id' => $userId,
            'imageable_type' => Comment::class,
            'imageable_id' => $comment->id,
            'name' => $tempImage->name,
            'extension' => $tempImage->extension,
            'size' => $tempImage->size,
        ]);
        $tempImage->delete();

        return $commentImage->id;
    }
}
