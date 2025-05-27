<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;

class NoteController extends Controller
{

    public function index()
    {


        $notes  = Note::all();

        if (count($notes) === 0) {
            return response()->json(['message' => 'No available notes'], 201);
        }


        return response()->json(['data' => $notes], 200);
    }


    public function store(StoreNoteRequest $request)
    {
        $note = Note::create($request->validated());
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
