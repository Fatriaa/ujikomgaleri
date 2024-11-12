<?php

// app/Models/Album.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    public function photos()
    {
        return $this->belongsToMany(Photo::class);
    }
}
