<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['photo_id', 'user_id', 'text'];

    public function photo()
    {
        return $this->belongsTo(Photo::class); // Assuming you have a Photo model
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    
}

