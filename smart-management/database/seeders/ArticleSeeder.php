<?php

namespace Database\Seeders;

use App\Models\Core\Article;
use App\Models\Financial\TaxRate;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garantir que existem taxas de IVA
        if (TaxRate::count() === 0) {
            $this->call(TaxRateSeeder::class);
        }

        $taxRateNormal = TaxRate::where('rate', 23)->first();
        $taxRateReduced = TaxRate::where('rate', 6)->first();

        $articles = [
            [
                'reference' => 'ART0001',
                'name' => 'Serviço de Consultoria',
                'description' => 'Consultoria especializada',
                'price' => 150.00,
                'tax_rate_id' => $taxRateNormal?->id,
                'status' => 'active',
            ],
            [
                'reference' => 'ART0002',
                'name' => 'Desenvolvimento Web',
                'description' => 'Desenvolvimento de website',
                'price' => 2500.00,
                'tax_rate_id' => $taxRateNormal?->id,
                'status' => 'active',
            ],
            [
                'reference' => 'ART0003',
                'name' => 'Manutenção Mensal',
                'description' => 'Serviço de manutenção mensal',
                'price' => 350.00,
                'tax_rate_id' => $taxRateNormal?->id,
                'status' => 'active',
            ],
            [
                'reference' => 'ART0004',
                'name' => 'Formação',
                'description' => 'Formação especializada',
                'price' => 500.00,
                'tax_rate_id' => $taxRateReduced?->id,
                'status' => 'active',
            ],
            [
                'reference' => 'ART0005',
                'name' => 'Licença Software',
                'description' => 'Licença anual de software',
                'price' => 1200.00,
                'tax_rate_id' => $taxRateNormal?->id,
                'status' => 'active',
            ],
        ];

        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['reference' => $article['reference']],
                $article
            );
        }

        // Criar mais 15 artigos aleatórios
        Article::factory(15)->create();
    }
}


