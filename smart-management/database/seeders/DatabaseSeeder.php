<?php

namespace Database\Seeders;

use App\Models\Catalog\ContactRole;
use App\Models\Core\Contact;
use App\Models\System\Company;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Core\Entity;
use Illuminate\Database\Seeder;
use App\Models\Catalog\Country;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            CountrySeeder::class,

        ]);
        Entity::factory(20)->create();
        ContactRole::factory(14)->create();
        Contact::factory(14)->create();
        Company::factory(1)->create();
    }
}
