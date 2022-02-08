<?php

namespace Database\Seeders;

use App\Models\Domino;
use App\Models\GameRoom;
use Illuminate\Database\Seeder;

class DominoesSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i <= 6; $i++) {
            for ($j = $i; $j <= 6; $j++) {
                Domino::create([
                    'side1' => $i,
                    'side2' => $j,
                ]);
            }
        }
    }
}
