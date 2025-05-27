<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use Illuminate\Support\Facades\Auth;


class NoteController extends Controller
{

    public function index(Request $request)
    {


        $notes  = Note::where('user_id' , $request->user()->id)->get();

        if (count($notes) === 0) {
            return response()->json(['message' => 'No available notes'], 201);
        }


        return response()->json(['data' => $notes], 200);
    }


    public function store(Request $request, StoreNoteRequest $payload)
    {
        $validatedData = $payload->validated();
        $validatedData['user_id'] = $request->user()->id;


        // Create the note with the merged data
        $note = Note::create($validatedData);
        return response()->json(['data' => $note], 201);
    }


    public function show(Note $note)
    {

        return response()->json(['data' => $note], 200);
    }


    public function update(UpdateNoteRequest $request, Note $note)
    {
        $note->update($request->validated());
        return response()->json(['data' => $note], 200);
    }


    public function destroy(Note $note)
    {
        $note->delete();
        return response()->json(null, 204);
    }
}
