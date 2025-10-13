<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Core\Order\Order;
use App\Models\Core\Order\SupplierOrder;
use App\Models\Core\Proposal\Proposal;
use App\Models\Core\WorkOrder;
use App\Models\Financial\Invoice\CustomerInvoice;
use App\Models\Financial\Invoice\SupplierInvoice;
use Illuminate\Support\Facades\Crypt;

class FixEncryptedNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:encrypted-numbers {--dry-run : Simular sem salvar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrigir números encriptados e atribuir números sequenciais corretos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🔍 MODO DRY-RUN: Nenhuma alteração será salva!');
        } else {
            $this->warn('⚠️  MODO REAL: Alterações serão salvas no banco!');
            if (!$this->confirm('Deseja continuar?')) {
                $this->info('❌ Operação cancelada.');
                return 0;
            }
        }

        $this->newLine();
        $this->info('🔧 Iniciando correção de números encriptados...');
        $this->newLine();

        // Models a corrigir
        $models = [
            'Orders' => Order::class,
            'Proposals' => Proposal::class,
            'Work Orders' => WorkOrder::class,
            'Customer Invoices' => CustomerInvoice::class,
            'Supplier Invoices' => SupplierInvoice::class,
            'Supplier Orders' => SupplierOrder::class,
        ];

        $totalFixed = 0;

        foreach ($models as $name => $modelClass) {
            $this->info("📋 Processando {$name}...");
            
            // Verificar se o model usa SoftDeletes
            $query = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive($modelClass))
                ? $modelClass::withTrashed()
                : $modelClass::query();
                
            $records = $query->get();
            $fixed = 0;
            $skipped = 0;

            foreach ($records as $record) {
                $currentNumber = $record->getRawOriginal('number'); // Pegar valor raw do DB

                // Verificar se está encriptado (começa com eyJ que é base64 de JSON)
                if (str_starts_with($currentNumber, 'eyJ')) {
                    try {
                        // Tentar decriptar
                        $decrypted = Crypt::decryptString($currentNumber);
                        
                        $this->line("  🔓 ID {$record->id}: '{$currentNumber}' → '{$decrypted}'");
                        
                        if (!$dryRun) {
                            // Atualizar diretamente no DB sem passar pelo Eloquent
                            \DB::table($record->getTable())
                                ->where('id', $record->id)
                                ->update(['number' => $decrypted]);
                        }
                        
                        $fixed++;
                    } catch (\Exception $e) {
                        $this->error("  ❌ ID {$record->id}: Erro ao decriptar - {$e->getMessage()}");
                    }
                } else if ($currentNumber === '000001' || is_null($currentNumber) || $currentNumber === '') {
                    // Número padrão ou vazio - gerar novo
                    $newNumber = $modelClass::nextNumber();
                    
                    $this->line("  🆕 ID {$record->id}: NULL/000001 → '{$newNumber}'");
                    
                    if (!$dryRun) {
                        \DB::table($record->getTable())
                            ->where('id', $record->id)
                            ->update(['number' => $newNumber]);
                    }
                    
                    $fixed++;
                } else {
                    // Número já está OK
                    $skipped++;
                }
            }

            $this->info("  ✅ {$name}: {$fixed} corrigidos, {$skipped} já estavam OK");
            $this->newLine();
            
            $totalFixed += $fixed;
        }

        $this->newLine();
        
        if ($dryRun) {
            $this->warn("🔍 DRY-RUN: {$totalFixed} registros SERIAM corrigidos");
            $this->info('💡 Execute sem --dry-run para aplicar as correções');
        } else {
            $this->info("✅ SUCESSO: {$totalFixed} registros corrigidos!");
        }

        return 0;
    }
}
