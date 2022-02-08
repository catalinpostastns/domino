<?php

namespace App\Events;

use App\Models\GameRoom;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GameStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string $message
     */
    public string $message;

    /**
     * @var GameRoom $game_room
     */
    public GameRoom $game_room;

    /**
     * @param string $message
     * @param GameRoom $gameRoom
     */
    public function __construct(string $message, GameRoom $gameRoom)
    {
        $this->message = $message;
        $this->game_room = $gameRoom;
    }

    /**
     * @return string[]
     */
    public function broadcastOn(): array
    {
        return [
            'game-room-' . $this->game_room->getId()
        ];
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'game-started';
    }
}
