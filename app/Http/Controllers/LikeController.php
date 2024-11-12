<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like; // Adjust the path based on your models location
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function like($photo_id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $userId = Auth::id();
    
        // Check if the user already liked this photo
        $like = Like::where('user_id', $userId)->where('photo_id', $photo_id)->first();
    
        if ($like) {
            $like->delete();
            return response()->json(['liked' => false, 'message' => 'Photo unliked']);
        } else {
            // Attempt to create a new like
            try {
                Like::create([
                    'user_id' => $userId,
                    'photo_id' => $photo_id,
                ]);
                return response()->json(['liked' => true, 'message' => 'Photo liked']);
            } catch (\Exception $e) {
                // Log the error if there’s an issue inserting
                \Log::error('Error creating like: ' . $e->getMessage());
                return response()->json(['liked' => false, 'message' => 'Failed to like photo']);
            }
        }
    }
    
    
    
}
