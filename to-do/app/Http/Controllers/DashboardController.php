<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // estatisticas
        $totalTasks = Task::where('user_id', $user->id)->count();
        $completedTasks = Task::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
        $pendingTasks = Task::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $overdueTasks = Task::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->count();

        // tarefas recentes
        $recentTasks = Task::where('user_id', $user->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'priority' => $task->priority,
                    'due_date' => $task->due_date,
                    'status' => $task->status,
                    'category' => $task->category ?? 'Geral',
                    'created_at' => $task->created_at,
                    'updated_at' => $task->updated_at,
                ];
            });

        // Progresso semanal (últimos 7 dias)
        $weeklyProgress = $this->getWeeklyProgress($user->id);

        return Inertia::render('Dashboard', [
            'stats' => [
                'total' => $totalTasks,
                'completed' => $completedTasks,
                'pending' => $pendingTasks,
                'overdue' => $overdueTasks,
            ],
            'recentTasks' => $recentTasks,
            'weeklyProgress' => $weeklyProgress,
        ]);
    }

    private function getWeeklyProgress($userId)
    {
        $progress = [];
        $days = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayTasks = Task::where('user_id', $userId)
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->count();

            $progress[] = [
                'day' => $days[$date->dayOfWeek],
                'tasks' => $dayTasks,
                'percentage' => min(100, $dayTasks * 20),
            ];
        }

        return $progress;
    }
}
