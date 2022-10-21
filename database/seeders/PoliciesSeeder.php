<?php

namespace Database\Seeders;

use App\Models\Policy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PoliciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Policy::create([
            'policy_name' => 'least recently used',
            'description' => null,
        ]);

        Policy::create([
            'policy_name' => 'random replacement',
            'description' => null,
        ]);
    }
}
