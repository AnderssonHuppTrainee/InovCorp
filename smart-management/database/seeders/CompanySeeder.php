<?php

namespace Database\Seeders;

use App\Models\System\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Smart Management, Lda.',
                'address' => 'Rua da Inovação, nº 123',
                'postal_code' => '1000-100',
                'city' => 'Lisboa',
                'tax_number' => '123456789',
                'phone' => '+351 21 123 4567',
                'email' => 'geral@smartmanagement.pt',
                'website' => 'https://smartmanagement.pt',
            ]
        );
    }
}


