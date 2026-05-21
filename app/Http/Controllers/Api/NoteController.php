<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Note;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\If_;
use App\Http\Resources\NoteResource;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            abort(401);
        }

        $notes = $user->notes()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'notes' => $notes,
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNoteRequest $request)
    {

        $data = $request->validated();

        $user = auth()->user();

        $note = $user->notes()->create([
            'title' => $data['title'],
            'content' => $data['content'],
        ]);

        return (new NoteResource($note))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        $user = auth()->user();

        if ($note->user_id !== $user->id) {
            abort(403, 'Acesso negado.');
        }

        return response()->json([
            'note' => $note,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UpdateNoteRequest $request,Note $note)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNoteRequest $request, Note $note)
    {
        $data = $request->validated();

        $user = auth()->user();

        if($note->user_id !== $user->id){
            abort(403, 'Acesso negado.');
        }

        $note->update($data);

        return (new NoteResource($note))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        $user = auth()->user();

        if ($note->user_id !== $user->id) {
            abort(403, 'Acesso negado.');
        }

        $note->delete();

        return response()->json([
            'message' => 'Nota removida com sucesso!'
        ], 200);
    }
}
