<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoomController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->id();

        $rooms = Room::with(['creator:id,name', 'users:id,name'])
            ->where('private', false) // salas públicas
            ->orWhereHas('users', function ($query) use ($userId) {
                $query->where('user_id', $userId); // salas privadas do usuário
            })
            ->orWhere('created_by', $userId) // salas criadas pelo usuário
            ->orderBy('created_at', 'desc')
            ->get();

        return $rooms;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request)
    {
        // Verificar autorização usando Policy
        $this->authorize('create', Room::class);

        try {
            $validated = $request->validated();

            $room = Room::create([
                'name' => $validated['name'],
                'private' => $validated['private'] ?? false,
                'created_by' => auth()->id(),
            ]);

            // Adicionar o criador como membro da sala
            $room->users()->syncWithoutDetaching([auth()->id()]);

            // Carregar relacionamentos para retorno
            $room->load(['creator:id,name', 'users:id,name']);

            return response()->json([
                'room' => $room,
                'message' => 'Sala criada com sucesso!'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTrace()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, Room $room)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        //
    }
}
