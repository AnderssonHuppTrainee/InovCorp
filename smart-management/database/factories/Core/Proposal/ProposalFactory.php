<?php

namespace Database\Factories\Core\Proposal;

use App\Models\Core\Proposal\Proposal;
use App\Models\Core\Article;
use App\Models\Core\Entity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Core\Proposal\Proposal>
 */
class ProposalFactory extends Factory
{
    protected $model = Proposal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $proposalDate = fake()->dateTimeBetween('-2 months', 'now');
        $validityDate = (clone $proposalDate)->modify('+30 days');

        // Criar client se não existir nenhum
        $client = Entity::clients()->inRandomOrder()->first()
            ?? Entity::factory()->create(['types' => ['client']]);

        return [
            'number' => Proposal::nextNumber(),
            'proposal_date' => $proposalDate,
            'client_id' => $client->id,
            'validity_date' => $validityDate,
            'status' => fake()->randomElement(['draft', 'closed']),
            'total_amount' => 0, // Será calculado pelos items
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Proposal $proposal) {
            // Criar 2-5 itens para a proposta
            $itemCount = rand(2, 5);

            for ($i = 0; $i < $itemCount; $i++) {
                $article = Article::where('status', 'active')->inRandomOrder()->first();
                $supplier = Entity::suppliers()->inRandomOrder()->first();

                if ($article) {
                    $quantity = rand(1, 10);
                    $unitPrice = $article->price;
                    $costPrice = $unitPrice * 0.7; // 30% margem

                    $proposal->items()->create([
                        'article_id' => $article->id,
                        'supplier_id' => $supplier?->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'cost_price' => $costPrice,
                    ]);
                }
            }

            // Recalcular total
            $proposal->calculateTotal();
        });
    }
}

