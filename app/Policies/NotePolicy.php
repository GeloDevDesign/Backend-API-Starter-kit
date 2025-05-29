<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NotePolicy
{

    public function modify(User $user, Note $note): bool
    {
        return $user->id === $note->user_id;

        //  return $user->id === $note->user_id ? Response::allow() : Response::deny('You do not own this note');
    }
}
