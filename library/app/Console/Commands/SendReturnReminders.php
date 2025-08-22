<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\BookRequest;
use App\Notifications\ReturnReminderNotification;

class SendReturnReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Envia lembretes de devolução no dia anterior ao vencimento';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->startOfDay();

        $requests = BookRequest::whereDate('expected_return_date', $tomorrow)
            ->where('status', 'approved')
            ->whereNull('returned_date')
            ->with(['user', 'book'])
            ->get();

        foreach ($requests as $request) {
            $request->user->notify(new ReturnReminderNotification($request));
            $this->info("Lembrete enviado para: {$request->user->email}");
        }

        $this->info("Total de lembretes enviados: {$requests->count()}");
    }
}
