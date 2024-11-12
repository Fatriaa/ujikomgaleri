<?php

// app/Http/Controllers/AlbumController.php
namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function addPhoto(Request $request)
    {
        $request->validate([
            'photo_id' => 'required|exists:photos,id',
        ]);

        $photo = Photo::find($request->photo_id);
        $album = Album::findOrCreate(['user_id' => auth()->id()]);

        $album->photos()->attach($photo);

        return response()->json(['message' => 'Photo added to album!'], 200);
    }

    // Add method to fetch album images (optional)
    public function getAlbum()
    {
        $album = auth()->user()->album;
        return response()->json($album->photos);
    }
}
