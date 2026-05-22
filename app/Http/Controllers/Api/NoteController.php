<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;

class NoteController extends Controller
{
    /**
     * Listar notas do usuário autenticado
     *
     * Retorna todas as notas do usuário logado com paginação.
     *
     * @group Notas
     * @authenticated
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Minha nota",
     *       "content": "Conteúdo da nota",
     *       "created_at": "2026-05-21 10:00:00",
     *       "updated_at": "2026-05-21 10:00:00"
     *     }
     *   ]
     * }
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

        return NoteResource::collection($notes);
    }

    /**
     * Criar nova nota
     *
     * Cria uma nova nota vinculada ao usuário autenticado.
     *
     * @group Notas
     * @authenticated
     *
     * @bodyParam title string required Título da nota. Example: Minha nota
     * @bodyParam content string Conteúdo da nota. Example: Texto da nota
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "title": "Minha nota",
     *     "content": "Texto da nota",
     *     "created_at": "2026-05-21 10:00:00",
     *     "updated_at": "2026-05-21 10:00:00"
     *   }
     * }
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
     * Exibir uma nota específica
     *
     * Retorna uma nota do usuário autenticado.
     *
     * @group Notas
     * @authenticated
     *
     * @urlParam note integer required ID da nota. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "title": "Minha nota",
     *     "content": "Conteúdo",
     *     "created_at": "2026-05-21 10:00:00",
     *     "updated_at": "2026-05-21 10:00:00"
     *   }
     * }
     *
     * @response 403 {
     *   "message": "Acesso negado."
     * }
     */
    public function show(Note $note)
    {
        $user = auth()->user();

        if ($note->user_id !== $user->id) {
            abort(403, 'Acesso negado.');
        }

        return new NoteResource($note);
    }

    /**
     * Atualizar nota
     *
     * Atualiza uma nota do usuário autenticado.
     *
     * @group Notas
     * @authenticated
     *
     * @urlParam note integer required ID da nota. Example: 1
     *
     * @bodyParam title string Título da nota. Example: Novo título
     * @bodyParam content string Conteúdo da nota. Example: Novo conteúdo
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "title": "Novo título",
     *     "content": "Novo conteúdo",
     *     "created_at": "2026-05-21 10:00:00",
     *     "updated_at": "2026-05-21 10:05:00"
     *   }
     * }
     *
     * @response 403 {
     *   "message": "Acesso negado."
     * }
     */
    public function update(UpdateNoteRequest $request, Note $note)
    {
        $data = $request->validated();

        $user = auth()->user();

        if ($note->user_id !== $user->id) {
            abort(403, 'Acesso negado.');
        }

        $note->update($data);

        return new NoteResource($note);
    }

    /**
     * Deletar nota
     *
     * Remove uma nota do usuário autenticado.
     *
     * @group Notas
     * @authenticated
     *
     * @urlParam note integer required ID da nota. Example: 1
     *
     * @response 200 {
     *   "message": "Nota removida com sucesso!"
     * }
     *
     * @response 403 {
     *   "message": "Acesso negado."
     * }
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
