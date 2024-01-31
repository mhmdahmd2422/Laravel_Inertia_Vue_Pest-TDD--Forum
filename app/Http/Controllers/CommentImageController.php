<?php

namespace App\Http\Controllers;

use App\Models\CommentImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommentImageController extends Controller
{
    public function __invoke(Request $request, CommentImage $commentImage)
    {
            Storage::disk('public')->delete('images/comments/'. $commentImage->name);
            $commentImage->delete();
    }
}
