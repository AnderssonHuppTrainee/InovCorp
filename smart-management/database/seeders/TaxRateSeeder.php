<?php

namespace Database\Seeders;

use App\Models\Financial\TaxRate;
use Illuminate\Database\Seeder;

class TaxRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxRates = [
            [
                'name' => 'IVA Normal (Continente)',
                'rate' => 23,
                'is_active' => true,
            ],
            [
                'name' => 'IVA Intermédio (Continente)',
                'rate' => 13,
                'is_active' => true,
            ],
            [
                'name' => 'IVA Reduzido (Continente)',
                'rate' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'IVA Isento',
                'rate' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'IVA Normal (Açores)',
                'rate' => 18,
                'is_active' => true,
            ],
            [
                'name' => 'IVA Reduzido (Açores)',
                'rate' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'IVA Normal (Madeira)',
                'rate' => 22,
                'is_active' => true,
            ],
            [
                'name' => 'IVA Reduzido (Madeira)',
                'rate' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($taxRates as $taxRate) {
            TaxRate::updateOrCreate(
                ['name' => $taxRate['name']],
                $taxRate
            );
        }
    }
}



