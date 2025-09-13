<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Room;
use App\Http\Requests\StoremessagesRequest;
use App\Http\Requests\UpdatemessagesRequest;
use Illuminate\Http\Request;
use App\Events\MessageSent;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Room $room)
    {
        return $room->messages()->with('sender:id,name')
            ->orderBy('created_at', 'asc')
            ->get();
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
    public function store(Request $request, Room $room)
    {
        $message = Message::create([
            'sender_id' => auth()->id(),
            'room_id' => $room->id,
            'body' => $request->body,
        ]);

        $message->load('sender:id,name');

        // broadcast em tempo real
        \Log::info('Disparando broadcast para mensagem:', ['message_id' => $message->id]);
        broadcast(new MessageSent($message))->toOthers();

        // Retorna JSON para requisições AJAX
        return response()->json($message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $messages)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $messages)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatemessagesRequest $request, Message $messages)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $messages)
    {
        //
    }
}
