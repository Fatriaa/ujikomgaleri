<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($photoId)
{
    $comments = Comment::with('user') // Assuming you have a relationship set up
        ->where('photo_id', $photoId)
        ->get();

    return response()->json($comments);
}

    public function store(Request $request)
    {
        $request->validate([
            'photo_id' => 'required|exists:photos,id',
            'text' => 'required|string|max:255',
        ]);
    
        $comment = Comment::create([
            'photo_id' => $request->photo_id,
            'user_id' => $request->user()->id, // Store the authenticated user's ID
            'text' => $request->text,
        ]);
    
        return response()->json($comment->load('user'), 201); // Load the user with the comment
    }
    
}

