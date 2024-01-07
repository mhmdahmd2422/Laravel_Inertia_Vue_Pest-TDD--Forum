<?php

namespace App\Http\Controllers;

use App\Models\TemporaryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeleteTemporaryImageController extends Controller
{
    public function __invoke($fileName)
    {
        $tempImage = TemporaryImage::where('name', $fileName)->first();
        if ($tempImage){
            Storage::disk('public')->delete('images/temp/'. $tempImage->name);
            $tempImage->delete();
        }

        return '';
    }
}
