<?php

namespace App\Events;

use App\Models\GameRoom;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GameFinished implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string $message
     */
    public string $message;

    /**
     * @var int game_room_id
     */
    public int $game_room_id;

    /**
     * @var int $game_room_status
     */
    public int $game_room_status;

    /**
     * @var string $winners_name
     */
    public string $winners_name;

    /**
     * @param string $message
     * @param GameRoom $gameRoom
     */
    public function __construct(string $message, GameRoom $gameRoom)
    {
        $this->message = $message;

        $this->game_room_id = $gameRoom->getId();
        $this->game_room_status = $gameRoom->getStatus();

        $this->winners_name = $gameRoom->getWinnersName();
    }

    /**
     * @return string[]
     */
    public function broadcastOn(): array
    {
        return [
            'game-room-' . $this->game_room_id
        ];
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'game-finished';
    }
}
