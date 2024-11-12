<?php

use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public routes
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Dashboard route
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/photos', [PhotoController::class, 'index'])->name('photos.index');
    Route::get('/uploads', [PhotoController::class, 'uploadIndex'])->name('uploads.index');
    Route::post('/upload-photos', [PhotoController::class, 'upload'])->middleware('auth');
    Route::post('/upload-photos', [PhotoController::class, 'store']);
    Route::post('/upload-photos', [PhotoController::class, 'uploadPhotos']);
    Route::post('/comments', [CommentController::class, 'store'])->middleware('auth'); // Ensure user is authenticated
    // You can remove or adjust this if needed
    // Route::get('/uploads', [PhotoController::class, 'index'])->name('uploads.index');
  // Comments
Route::get('/photos/{photo}/comments', [CommentController::class, 'index']);

// Likes
Route::post('/photos/{photo}/like', [LikeController::class, 'like'])->name('photos.like');
Route::get('/your-photos', [PhotoController::class, 'yourPhotos'])->name('your-photos')->middleware(['auth']);
Route::post('/photos/update/{id}', [PhotoController::class, 'update'])->name('photos.update');
Route::delete('/photos/delete/{photo}', [PhotoController::class, 'delete'])->name('photos.delete');
Route::post('/albums/add', [AlbumController::class, 'addPhoto']);




});

require __DIR__.'/auth.php';
