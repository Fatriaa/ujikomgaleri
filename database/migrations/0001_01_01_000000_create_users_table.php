<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel users (bawaan Laravel Breeze)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('nama_lengkap')->nullable();  // Full name field
            $table->text('alamat')->nullable();           // Address field
            $table->rememberToken();
            $table->timestamps();
        });

        // Tabel password_reset_tokens (bawaan Laravel Breeze)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Tabel sessions (bawaan Laravel Breeze)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // Foreign key constraint
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Tabel albums
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Tabel photos
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->string('src');        
            $table->string('judul');        
            $table->string('deskripsi');        
            $table->string('alt')->nullable(); // Optional alt text for the photo
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Delete photos if the user is deleted
            $table->timestamps();
        });

        // Tabel like_photos
        Schema::create('like_photos', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key reference to users
            $table->foreignId('photo_id')->constrained('photos')->onDelete('cascade'); // Foreign key reference to photos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('like_photos');
        Schema::dropIfExists('photos');
        Schema::dropIfExists('albums');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
