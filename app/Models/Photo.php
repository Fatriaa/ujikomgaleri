<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'src',
        'judul',
        'deskripsi',
        'alt',
        'user_id',
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Explicitly specifying user_id
    }
      public function albums()
    {
        return $this->belongsToMany(Album::class);
    }
}
