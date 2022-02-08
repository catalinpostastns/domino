<?php

namespace App\Events;

use App\Models\GameRoom;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UpdateRoom implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string $message
     */
    public string $message;

    /**
     * @var array $game_room
     */
    public array $game_room;

    /**
     * @param string $message
     * @param GameRoom $gameRoom
     */
    public function __construct(string $message, GameRoom $gameRoom)
    {
        $this->message = $message;
        $this->game_room = $gameRoom->setAppends(['status_name', 'number_of_users', 'allowed_to_join'])->toArray();
    }

    /**
     * @return string[]
     */
    public function broadcastOn(): array
    {
        return [
            'game-rooms'
        ];
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'update-room';
    }
}
