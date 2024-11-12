<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PhotoUploadController extends Controller
{
    public function index()
    {
        return Inertia::render('Uploads'); // This should correspond to your Vue component
    }

    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Photo upload logic here

        return redirect()->route('uploads')->with('success', 'Photo uploaded successfully!');
    }
}

