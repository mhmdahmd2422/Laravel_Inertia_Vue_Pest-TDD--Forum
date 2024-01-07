<?php

namespace App\Http\Controllers;

use App\Models\TemporaryImage;
use http\Env\Response;
use Illuminate\Http\Request;

class UploadTemporaryImageController extends Controller
{
    public function __invoke(Request $request)
    {
        if(!$request->hasFile('image')){
            return response()->json(['error' => 'There is no image present'], 400);
        }

        $request->validate([
           'image' => ['required', 'file', 'image', 'extensions:jpeg,png'],
        ]);

        $path = $request->file('image')->store('public/images/temp');
        if(!$path){
            return response()->json(['error', 'The file cannot be saved.'], 500);
        }
        $uploadedFile = $request->file('image');
        $image = TemporaryImage::create([
           'name' =>  $uploadedFile->hashName(),
            'extension' => $uploadedFile->extension(),
            'size' => $uploadedFile->getSize(),
        ]);

        return $image->name;
    }

    public function upload()

    {

    }
}
