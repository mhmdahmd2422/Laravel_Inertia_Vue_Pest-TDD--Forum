<?php

namespace App\Http\Controllers;

use App\Models\TemporaryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemporaryImageController extends Controller
{
    public function store(Request $request)
    {
        if(!$request->hasFile('image')){
            return response()->json(['error' => 'There is no image present'], 400);
        }

        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png'],
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

    public function destroy($fileName)
    {
        $tempImage = TemporaryImage::where('name', $fileName)->first();
        if ($tempImage){
            Storage::disk('public')->delete('images/temp/'. $tempImage->name);
            $tempImage->delete();
        }

        return '';
    }
}
