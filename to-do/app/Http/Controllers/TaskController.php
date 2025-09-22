<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //listar tarefas
        $query = Task::query(); //faz uma new query

        if ($request->has('status')) {
            $query->where('status', $request->status); //filtro por status
        }
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);//prority
        }

        if ($request->has('due_date')) {
            $query->whereDate('due_date', $request->due_date);//data de vencimento
        }

        $query->orderBy('due_date', 'asc')->get();

        return response()->json($query); //data pra api
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $validated = $request->validated();

        $task = Task::create($validated);

        return response()->json($task, 201); //envia o http status

    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTaskRequest $request, Task $task)
    {
        $validated = $request->validated();

        $task->update($validated);

        return response()->json($task);
    }
    //marca como complete
    public function complete(Task $task)
    {
        $task->update(['status' => 'completed']);
        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(null, 204);
    }

}
