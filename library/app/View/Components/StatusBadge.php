<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatusBadge extends Component
{

    public $status;
    public function __construct($status)
    {
        $this->status = $status;
    }
    public function classes()
    {
        return match ($this->status) {
            'approved' => 'badge-success text-white',
            'paid' => 'badge-success text-white',
            'pending', 'pending_returned' => 'badge-warning text-white',
            'returned' => 'badge-success text-white',
            'rejected' => 'badge-error text-white',
            'failed' => 'badge-error text-white',
            'cancelled' => 'badge-neutral text-white',
            default => 'badge-neutral text-white',
        };
    }

    public function icon()
    {
        return match ($this->status) {
            'approved' => 'fas fa-check-circle',
            'paid' => 'fas fa-check-circle',
            'failed' => 'fas fa-times-circle',
            'pending' => 'fas fa-clock',
            'pending_returned' => 'fas fa-clock',
            'returned' => 'fas fa-undo',
            'rejected' => 'fas fa-times-circle',
            'cancelled' => 'fas fa-ban',
            default => null,
        };
    }

    public function label()
    {
        return match ($this->status) {
            'approved' => 'Aprovado',
            'pending' => 'Pendente',
            'pending_returned' => 'Devolução Pendente',
            'returned' => 'Devolvido',
            'rejected' => 'Rejeitado',
            'paid' => 'Pago',
            'failed' => 'Falhou',
            'cancelled' => 'Cancelado',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.status-badge');
    }
}
