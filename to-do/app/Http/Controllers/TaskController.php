<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use Illuminate\Http\Request;
use App\Models\Task;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        //listar tarefas
        $tasks = Task::query()
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('due_date', 'asc')
            ->paginate(3)
            ->withQueryString();

        return Inertia::render('tasks/Index', [
            'tasks' => $tasks,
            'filters' => $request->only('search')
        ]);
    }

    public function create()
    {
        return Inertia::render('tasks/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $validated = $request->validated();

        $task = Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Tarefa criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function edit(Task $task)
    {

        return Inertia::render('tasks/Edit', [
            'task' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTaskRequest $request, Task $task)
    {
        $validated = $request->validated();

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Tarefa atualizada com sucesso!');
    }

    //marca como complete
    public function complete(Task $task)
    {
        $task->update(['status' => 'completed']);
        return back()->with('success', 'Tarefa completada!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Tarefa deletada com sucesso!');

    }

}
