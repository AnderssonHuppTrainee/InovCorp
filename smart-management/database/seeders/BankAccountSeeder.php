<?php

namespace Database\Seeders;

use App\Models\Financial\BankAccount;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Conta Principal',
                'bank_name' => 'Millennium BCP',
                'account_number' => '0035123456789',
                'iban' => 'PT50003512345678901234567',
                'swift' => 'BCOMPTPL',
                'balance' => 50000.00,
                'currency' => 'EUR',
                'is_active' => true,
            ],
            [
                'name' => 'Conta Operacional',
                'bank_name' => 'Caixa Geral de Depósitos',
                'account_number' => '0003987654321',
                'iban' => 'PT50000398765432109876543',
                'swift' => 'CGDIPTPL',
                'balance' => 25000.00,
                'currency' => 'EUR',
                'is_active' => true,
            ],
        ];

        foreach ($accounts as $account) {
            BankAccount::updateOrCreate(
                ['account_number' => $account['account_number']],
                $account
            );
        }
    }
}

