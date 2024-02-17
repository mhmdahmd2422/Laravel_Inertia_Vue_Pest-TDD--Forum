<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class CommentImageController extends Controller
{
    public function destroy(Image $image): void
    {
            Storage::disk('public')->delete('images/comments/'. $image->name);
            $image->delete();
    }
}
