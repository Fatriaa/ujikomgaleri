<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $table = 'likes_photos'; // Ensure table name matches
    protected $fillable = ['user_id', 'photo_id'];
}


