<?php

namespace Database\Seeders;

use App\Models\Core\Article;
use App\Models\Core\Order\Order;
use App\Models\Core\Order\SupplierOrder;
use App\Models\Core\Proposal\Proposal;
use App\Models\Core\WorkOrder;
use App\Models\Financial\BankAccount;
use App\Models\Financial\Invoice\CustomerInvoice;
use App\Models\Financial\Invoice\SupplierInvoice;
use App\Models\System\Calendar\CalendarEvent;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seed da base de dados...');

        // 1. Dados base (configurações)
        $this->command->info('📍 1/8 - Criando países...');
        $this->call(CountrySeeder::class);

        $this->command->info('💼 2/8 - Criando funções de contactos...');
        $this->call(ContactRoleSeeder::class);

        $this->command->info('💵 3/8 - Criando taxas de IVA...');
        $this->call(TaxRateSeeder::class);

        $this->command->info('📅 4/8 - Criando tipos e ações de calendário...');
        $this->call(CalendarEventTypeSeeder::class);
        $this->call(CalendarActionSeeder::class);

        $this->command->info('🏢 5/8 - Criando dados da empresa...');
        $this->call(CompanySeeder::class);

        // 2. Utilizadores e Permissões
        $this->command->info('👥 6/8 - Criando utilizadores e permissões...');
        $this->call(PermissionSeeder::class);
        $this->call(UserSeeder::class);

        // 3. Dados transacionais
        $this->command->info('🏬 7/8 - Criando entidades (clientes/fornecedores)...');
        \App\Models\Core\Entity::factory(30)->create();

        $this->command->info('📞 Criando contactos...');
        \App\Models\Core\Contact::factory(50)->create();

        $this->command->info('📦 8/8 - Criando artigos...');
        $this->call(ArticleSeeder::class);

        // 4. Propostas e Encomendas
        $this->command->info('📄 Criando propostas...');
        Proposal::factory(15)->create();

        $this->command->info('🛒 Criando encomendas de clientes...');
        Order::factory(20)->create();

        $this->command->info('📦 Criando encomendas de fornecedores...');
        SupplierOrder::factory(15)->create();

        // 5. Ordens de Trabalho
        $this->command->info('📋 Criando ordens de trabalho...');
        WorkOrder::factory(10)->create();

        // 6. Eventos de Calendário
        $this->command->info('📅 Criando eventos de calendário...');
        CalendarEvent::factory(30)->create();

        // 7. Financeiro
        $this->command->info('🏦 Criando contas bancárias...');
        $this->call(BankAccountSeeder::class);
        BankAccount::factory(3)->create();

        $this->command->info('💰 Criando faturas de clientes...');
        CustomerInvoice::factory(25)->create();

        $this->command->info('💸 Criando faturas de fornecedores...');
        SupplierInvoice::factory(20)->create();

        // 8. Arquivo Digital (opcional - não criar ficheiros reais)
        // DigitalArchive::factory(10)->create();

        $this->command->info('✅ Seed concluído com sucesso!');
        $this->command->newLine();
        $this->command->info('📊 Dados criados:');
        $this->command->info('   - 30 Entidades (Clientes/Fornecedores)');
        $this->command->info('   - 50 Contactos');
        $this->command->info('   - 20 Artigos');
        $this->command->info('   - 15 Propostas');
        $this->command->info('   - 20 Encomendas Clientes');
        $this->command->info('   - 15 Encomendas Fornecedores');
        $this->command->info('   - 10 Ordens de Trabalho');
        $this->command->info('   - 30 Eventos de Calendário');
        $this->command->info('   - 5 Contas Bancárias');
        $this->command->info('   - 25 Faturas Clientes');
        $this->command->info('   - 20 Faturas Fornecedores');
        $this->command->info('   - 7+ Utilizadores');
        $this->command->newLine();
        $this->command->info('🔑 Credenciais de acesso:');
        $this->command->info('   Admin: admin@smartmanagement.pt / password');
        $this->command->info('   User:  user@smartmanagement.pt / password');
    }
}
