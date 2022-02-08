<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\GameRoomUser
 *
 * @property int $id
 * @property int $game_room_id
 * @property int $user_id
 * @property int $index
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GameRoom $gameRoom
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomUser whereGameRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomUser whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomUser whereUserId($value)
 * @mixin \Eloquent
 */
class GameRoomUser extends Model
{
    use HasFactory;

    protected $table = 'game_room_user';

    /**
     * @return BelongsTo
     */
    public function gameRoom(): BelongsTo
    {
        return $this->belongsTo(GameRoom::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $value
     *
     * @return void
     */
    public function setIndex(int $value)
    {
        $this->index = $value;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $userIndex = self::where('game_room_id', $model->game_room_id)->max('index');
            if ($userIndex === null) {
                $userIndex = 1;
            } else {
                $userIndex++;
            }

            $model->index = $userIndex;
        });
    }
}
