<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


use App\Models\Bedelias;


class BedeliasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bedelias::factory()->count(2)->create();
    }
}
