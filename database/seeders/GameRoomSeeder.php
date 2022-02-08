<?php

namespace Database\Seeders;

use App\Models\GameRoom;
use Illuminate\Database\Seeder;

class GameRoomSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        GameRoom::create();
    }
}
