<?php

namespace App\Http\Controllers;

use App\Models\CommentImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommentImageController extends Controller
{
    public function destroy(CommentImage $commentImage): void
    {
            Storage::disk('public')->delete('images/comments/'. $commentImage->name);
            $commentImage->delete();
    }
}
