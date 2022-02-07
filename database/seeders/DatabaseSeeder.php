<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Profile::factory(1)->create();

        Role::create([
            "name" => "invited",
            "description" => "User invited by the admin"
        ]);
    }
}
