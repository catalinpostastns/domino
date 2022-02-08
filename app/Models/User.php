<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Domino[] $dominoes
 * @property-read int|null $dominoes_count
 * @property-read \App\Models\GameRoom|null $gameRoom
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Collection|\App\Models\GameRoomDomino[] $gameRoomDominoes
 * @property-read int|null $game_room_dominoes_count
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return HasOneThrough
     */
    public function gameRoom(): HasOneThrough
    {
        return $this->hasOneThrough(GameRoom::class, GameRoomUser::class,
            'user_id', 'id', 'id', 'game_room_id');
    }

    /**
     * @return GameRoom|null
     */
    public function getGameRoom(): ?GameRoom
    {
        return $this->gameRoom;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return HasMany
     */
    public function gameRoomDominoes(): HasMany
    {
        return $this->hasMany(GameRoomDomino::class);
    }

    /**
     * @return Collection
     */
    public function getGameRoomDominoes(): Collection
    {
        return $this->gameRoomDominoes;
    }

    /**
     * @return Collection
     */
    public function getNotPlacedGameRoomDominoes(): Collection
    {
        return $this->gameRoomDominoes()->notPlaced()->get();
    }

    /**
     * @return bool
     */
    public function noDominoesRemaining(): bool
    {
        return !$this->gameRoomDominoes()->whereNull('index_position')->exists();
    }

    /**
     * @return bool
     */
    public function maximumNumberOfSelectedDominoes(): bool
    {
        return $this->gameRoomDominoes()->count() >= 7;
    }

    /**
     * @return bool
     */
    public function hasGameRoom(): bool
    {
        return $this->gameRoom()->exists();
    }

    /**
     * @return int
     */
    public function getDominoesSum(): int
    {
        $sum = 0;

        $dominoes = $this->getGameRoomDominoes();
        foreach ($dominoes as $domino) {
            $sum += $domino->getSum();
        }

        return $sum;
    }
}
