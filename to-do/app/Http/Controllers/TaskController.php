<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
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
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();
        $priority = $request->string('priority')->toString();
        $dueFrom = $request->string('due_from')->toString();
        $dueTo = $request->string('due_to')->toString();
        $sortBy = $request->input('sort_by', 'due_date');
        $sortDir = $request->input('sort_dir', 'asc');
        $perPage = (int) $request->input('per_page', 10);

        // listar tarefas com filtros
        $tasks = Task::query()
            ->select(['id', 'title', 'priority', 'due_date', 'status', 'created_at'])
            ->when(auth()->check(), function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query) use ($status) {
                if (in_array($status, ['pending', 'completed'], true)) {
                    $query->where('status', $status);
                }
            })
            ->when($priority, function ($query) use ($priority) {
                if (in_array($priority, ['low', 'medium', 'high'], true)) {
                    $query->where('priority', $priority);
                }
            })
            ->when($dueFrom, function ($query) use ($dueFrom) {
                $query->whereDate('due_date', '>=', $dueFrom);
            })
            ->when($dueTo, function ($query) use ($dueTo) {
                $query->whereDate('due_date', '<=', $dueTo);
            })
            ->when($sortBy, function ($query) use ($sortBy, $sortDir) {
                $allowed = ['due_date', 'priority', 'title', 'created_at'];
                $direction = strtolower($sortDir) === 'desc' ? 'desc' : 'asc';
                $column = in_array($sortBy, $allowed, true) ? $sortBy : 'due_date';
                $query->orderBy($column, $direction);
            }, function ($query) {
                $query->orderBy('due_date', 'asc');
            })
            ->paginate(max($perPage, 1))
            ->withQueryString();

        return Inertia::render('tasks/Index', [
            'tasks' => $tasks,
            'filters' => [
                'search' => $search ?: null,
                'status' => $status ?: null,
                'priority' => $priority ?: null,
                'due_from' => $dueFrom ?: null,
                'due_to' => $dueTo ?: null,
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
                'per_page' => $perPage,
            ],
        ]);
    }

    public function create()
    {

        return Inertia::render('tasks/Create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return Inertia::render('tasks/Show', [
            'task' => $task,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $validated = $request->validated();

        $task = Task::create(array_merge($validated, [
            'user_id' => auth()->id(),
        ]));

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
    public function update(UpdateTaskRequest $request, Task $task)
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
