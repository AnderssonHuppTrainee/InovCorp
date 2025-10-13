<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Models\Core\Entity;

class FixEntityTaxNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:entity-tax-numbers {--dry-run : Executar em modo teste sem salvar alterações}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Decripta tax_numbers encriptados nas entities para permitir buscas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 MODO DRY-RUN: Nenhuma alteração será salva!');
        } else {
            if (!$this->confirm('⚠️  MODO REAL: Alterações serão salvas no banco! Deseja continuar?')) {
                $this->info('❌ Operação cancelada.');
                return Command::SUCCESS;
            }
        }

        $this->info('🔧 Iniciando correção de tax_numbers encriptados...');

        // Buscar todas as entities (incluindo soft deleted)
        $entities = Entity::withTrashed()->get();
        $fixed = 0;
        $skipped = 0;
        $errors = 0;

        $this->info("📊 Total de entities encontradas: {$entities->count()}");

        foreach ($entities as $entity) {
            // Obter o valor RAW do banco (antes de aplicar o cast)
            $rawTaxNumber = DB::table('entities')
                ->where('id', $entity->id)
                ->value('tax_number');

            // Verificar se está encriptado (começa com eyJ que é base64 de JSON)
            if (is_string($rawTaxNumber) && str_starts_with($rawTaxNumber, 'eyJ')) {
                try {
                    // Tentar decriptar
                    $decryptedTaxNumber = Crypt::decryptString($rawTaxNumber);
                    
                    if (!$isDryRun) {
                        // Atualizar diretamente no banco sem usar o modelo
                        // (para evitar re-encriptar)
                        DB::table('entities')
                            ->where('id', $entity->id)
                            ->update(['tax_number' => $decryptedTaxNumber]);
                        
                        $this->info("  ✅ Entity #{$entity->id} ({$entity->name}): '{$rawTaxNumber}' → '{$decryptedTaxNumber}'");
                    } else {
                        $this->info("  🔓 Entity #{$entity->id} ({$entity->name}): seria decriptado para '{$decryptedTaxNumber}'");
                    }
                    
                    $fixed++;
                } catch (\Exception $e) {
                    $this->error("  ❌ Entity #{$entity->id} ({$entity->name}): Erro ao decriptar - {$e->getMessage()}");
                    $errors++;
                }
            } else {
                // Já está em texto plano
                $this->line("  ℹ️  Entity #{$entity->id} ({$entity->name}): tax_number já está em texto plano ('{$rawTaxNumber}')");
                $skipped++;
            }
        }

        $this->newLine();
        $this->info('📊 RESUMO:');
        $this->info("  ✅ Decriptados: {$fixed}");
        $this->info("  ℹ️  Já em texto plano: {$skipped}");
        $this->info("  ❌ Erros: {$errors}");

        if ($isDryRun) {
            $this->newLine();
            $this->warn('⚠️  Isto foi uma simulação! Execute sem --dry-run para aplicar as alterações.');
        } else {
            $this->newLine();
            $this->info('✅ Correção concluída! Agora a busca por NIF funcionará corretamente.');
        }

        return Command::SUCCESS;
    }
}
