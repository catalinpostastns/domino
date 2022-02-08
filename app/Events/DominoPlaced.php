<?php

namespace App\Events;

use App\Models\GameRoom;
use App\Models\GameRoomDomino;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DominoPlaced implements ShouldBroadcast
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
     * @var GameRoomDomino $game_room_domino
     */
    public GameRoomDomino $game_room_domino;

    /**
     * @param string $message
     * @param GameRoom $gameRoom
     * @param GameRoomDomino $gameRoomDomino
     */
    public function __construct(string $message, GameRoom $gameRoom, GameRoomDomino $gameRoomDomino)
    {
        $this->message = $message;
        $this->game_room = $gameRoom;
        $this->game_room_domino = $gameRoomDomino;
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
        return 'domino-placed';
    }
}
