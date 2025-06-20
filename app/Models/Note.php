<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    //
    protected  $fillable = ['title', 'body'];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
