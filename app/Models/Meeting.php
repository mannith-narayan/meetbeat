<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Meeting extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'audio_file',
        'user_id',
        'transcript',
    ];

    /**
     * Get the user that owns the meeting.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
