<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Note extends Model
{
    //
    protected  $fillable = ['title', 'body','user_id'];


    // public function user(): BelongsTo
    // {
    //     return $this->belongsTo(User::class);
    // }
}
