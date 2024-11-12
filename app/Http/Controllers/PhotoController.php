<?php
namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PhotoController extends Controller
{
    public function index()
    {
        // Fetch photos with associated user info including profile picture
        $photos = Photo::with('user:id,username,profile_picture')->get(); // Include user relationship
        return response()->json($photos);
    }
    
    public function uploadIndex()
    {
        // Fetch photos with associated user info including profile picture
        $photos = Photo::with('user:id,username,profile_picture')->get(); // Include user relationship

        // Return Inertia response with photos
        return Inertia::render('Uploads', [
            'photos' => $photos, // Pass photos to the Inertia view
        ]);
    }

    public function uploadPhotos(Request $request)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'judul.*' => 'required|string|max:255',
            'deskripsi.*' => 'nullable|string|max:255',
        ]);

        $uploadedImages = [];
        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('photos', 'public');

            // Create a new photo record in the database
            $photo = Photo::create([
                'src' => Storage::url($path),
                'judul' => $request->judul[$index],
                'deskripsi' => $request->deskripsi[$index],
                'user_id' => auth()->id(), // Associate the photo with the authenticated user
            ]);

            $uploadedImages[] = $photo; // Store the created photo object for response
        }

        // Redirect back to the uploads page with success message
        return redirect()->route('uploads.index')->with('success', 'Images uploaded successfully!');
    }

    public function like($id)
    {
        try {
            // Your like logic here
        } catch (\Exception $e) {
            Log::info('Like photo called with ID: ' . $id);
            return response()->json(['error' => 'Error liking photo'], 500);
        }
    }

    public function yourPhotos(Request $request)
    {
        // Fetch the authenticated user's photos
        $photos = Photo::where('user_id', $request->user()->id)->get();

        // Return the Inertia response with the photos
        return Inertia::render('YourFoto', [
            'photos' => $photos,
        ]);
    }
 public function update(Request $request, $id)
    {
        // Validate incoming data
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Find the photo by ID
        $photo = Photo::find($id);

        if (!$photo) {
            return response()->json(['message' => 'Photo not found'], 404);
        }

        // Update title and description
        $photo->judul = $request->input('title');
        $photo->deskripsi = $request->input('description');

        // Handle image upload if present
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if (Storage::exists($photo->src)) {
                Storage::delete($photo->src);
            }

            // Store the new image
            $path = $request->file('image')->store('photos', 'public');
            $photo->src = '/storage/' . $path; // Save the new image path
        }

        // Save the updated photo
        $photo->save();

        // Return the updated photo
        return response()->json(['message' => 'Photo updated successfully', 'photo' => $photo], 200);
    }
    public function delete($photoId)
    {
        $photo = Photo::findOrFail($photoId);

        // Delete the photo from the storage if it exists
        if (Storage::exists($photo->src)) {
            Storage::delete($photo->src);
        }

        // Delete the photo record from the database
        $photo->delete();

        return response()->json(['message' => 'Photo deleted successfully']);
    }
}

