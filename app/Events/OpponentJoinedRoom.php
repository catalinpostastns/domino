<?php

namespace App\Events;

use App\Models\GameRoom;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OpponentJoinedRoom implements ShouldBroadcast
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
     * @var User $user
     */
    public User $user;

    /**
     * @param string $message
     * @param GameRoom $gameRoom
     * @param User $user
     */
    public function __construct(string $message, GameRoom $gameRoom, User $user)
    {
        $this->message = $message;
        $this->game_room = $gameRoom;
        $this->user = $user;
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
        return 'opponent-joined-room';
    }
}
