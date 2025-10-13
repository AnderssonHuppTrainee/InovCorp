<?php

namespace App\Http\Controllers;

use App\Models\Core\Entity;
use App\Models\Core\Proposal\Proposal;
use App\Models\Core\Order\Order;
use App\Models\Core\WorkOrder;
use App\Models\Financial\Invoice\CustomerInvoice;
use App\Models\Financial\Invoice\SupplierInvoice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Estatísticas de Entities
        $totalClients = Entity::clients()->count();
        $totalSuppliers = Entity::suppliers()->count();
        $activeEntities = Entity::active()->count();

        // Estatísticas de Vendas
        $totalProposals = Proposal::count();
        $draftProposals = Proposal::draft()->count();
        $totalOrders = Order::count();
        $draftOrders = Order::draft()->count();

        // Estatísticas de Work Orders
        $totalWorkOrders = WorkOrder::count();
        $pendingWorkOrders = WorkOrder::pending()->count();
        $inProgressWorkOrders = WorkOrder::inProgress()->count();

        // Estatísticas Financeiras
        $totalCustomerInvoices = CustomerInvoice::count();
        $pendingCustomerInvoices = CustomerInvoice::sent()->count();
        $overdueCustomerInvoices = CustomerInvoice::overdue()->count();
        $paidCustomerInvoices = CustomerInvoice::paid()->count();

        $totalSupplierInvoices = SupplierInvoice::count();
        $pendingSupplierInvoices = SupplierInvoice::pendingPayment()->count();
        $overdueSupplierInvoices = SupplierInvoice::overdue()->count();
        $paidSupplierInvoices = SupplierInvoice::paid()->count();

        // Totais financeiros
        $totalRevenue = CustomerInvoice::paid()->sum('total_amount') ?? 0;
        $pendingRevenue = CustomerInvoice::whereIn('status', ['sent', 'partially_paid'])->sum('balance') ?? 0;
        $totalExpenses = SupplierInvoice::paid()->sum('total_amount') ?? 0;
        $pendingExpenses = SupplierInvoice::pendingPayment()->sum('total_amount') ?? 0;

        // Atividades recentes (últimas 10)
        $recentProposals = Proposal::with('client')
            ->latest()
            ->take(5)
            ->get(['id', 'number', 'client_id', 'total_amount', 'status', 'created_at']);

        $recentOrders = Order::with('client')
            ->latest()
            ->take(5)
            ->get(['id', 'number', 'client_id', 'total_amount', 'status', 'created_at']);

        $recentWorkOrders = WorkOrder::with('client', 'assignedUser')
            ->latest()
            ->take(5)
            ->get(['id', 'number', 'title', 'client_id', 'assigned_to', 'status', 'priority', 'created_at']);

        // Vendas por mês (últimos 6 meses)
        $salesByMonth = Order::select(
            DB::raw('DATE_FORMAT(order_date, "%Y-%m") as month'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('order_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'entities' => [
                    'total_clients' => $totalClients,
                    'total_suppliers' => $totalSuppliers,
                    'active_entities' => $activeEntities,
                ],
                'sales' => [
                    'total_proposals' => $totalProposals,
                    'draft_proposals' => $draftProposals,
                    'total_orders' => $totalOrders,
                    'draft_orders' => $draftOrders,
                ],
                'work_orders' => [
                    'total' => $totalWorkOrders,
                    'pending' => $pendingWorkOrders,
                    'in_progress' => $inProgressWorkOrders,
                ],
                'financials' => [
                    'customer_invoices' => [
                        'total' => $totalCustomerInvoices,
                        'pending' => $pendingCustomerInvoices,
                        'overdue' => $overdueCustomerInvoices,
                        'paid' => $paidCustomerInvoices,
                    ],
                    'supplier_invoices' => [
                        'total' => $totalSupplierInvoices,
                        'pending' => $pendingSupplierInvoices,
                        'overdue' => $overdueSupplierInvoices,
                        'paid' => $paidSupplierInvoices,
                    ],
                    'revenue' => [
                        'total' => $totalRevenue,
                        'pending' => $pendingRevenue,
                    ],
                    'expenses' => [
                        'total' => $totalExpenses,
                        'pending' => $pendingExpenses,
                    ],
                ],
            ],
            'recent_activities' => [
                'proposals' => $recentProposals,
                'orders' => $recentOrders,
                'work_orders' => $recentWorkOrders,
            ],
            'charts' => [
                'sales_by_month' => $salesByMonth,
            ],
        ]);
    }
}

