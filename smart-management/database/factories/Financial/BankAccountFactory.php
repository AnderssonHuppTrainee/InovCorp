<?php

namespace Database\Factories\Financial;

use App\Models\Financial\BankAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Financial\BankAccount>
 */
class BankAccountFactory extends Factory
{
    protected $model = BankAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $banks = ['Millennium BCP', 'Caixa Geral de Depósitos', 'Santander', 'Novo Banco', 'BPI'];

        return [
            'name' => fake()->words(2, true) . ' Account',
            'bank_name' => fake()->randomElement($banks),
            'account_number' => fake()->numerify('############'),
            'iban' => 'PT50' . fake()->numerify('####################'),
            'swift' => fake()->lexify('????????'),
            'balance' => fake()->randomFloat(2, 1000, 50000),
            'currency' => 'EUR',
            'is_active' => fake()->boolean(90),
        ];
    }
}

