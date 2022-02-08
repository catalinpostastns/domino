<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * App\Models\GameRoom
 *
 * @property int $id
 * @property int $status
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Domino[] $dominoes
 * @property-read int|null $dominoes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GameRoomDomino[] $gameRoomDominoes
 * @property-read int|null $game_room_dominoes_count
 * @property-read string $status_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $opponents
 * @property-read int|null $opponents_count
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoom newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoom query()
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoom whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoom whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoom whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoom whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoom whereUserId($value)
 * @mixin \Eloquent
 * @property int|null $winner_id
 * @property-read Collection|\App\Models\GameRoomUser[] $gameRoomUsers
 * @property-read int|null $game_room_users_count
 * @property-read \App\Models\User|null $winner
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoom whereWinnerId($value)
 */
class GameRoom extends Model
{
    use HasFactory;

    const STATUS_LOBBY = 0;
    const STATUS_SELECTION = 1;
    const STATUS_STARTED = 2;
    const STATUS_FINISHED = 3;

    const STATUS_NAME = [
        self::STATUS_LOBBY => 'Lobby',
        self::STATUS_SELECTION => 'Selection',
        self::STATUS_STARTED => 'Started',
        self::STATUS_FINISHED => 'Finished',
    ];

    protected $fillable = [];

    protected $appends = [
        'status_name'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class, 'game_room_user', 'game_room_id', 'user_id'
        );
    }

    /**
     * @return int
     */
    public function getNumberOfUsersAttribute(): int
    {
        return $this->users()->count();
    }

    /**
     * @return bool
     */
    public function getAllowedToJoinAttribute(): bool
    {
        return (!$this->maximumNumberOfPlayers() && ($this->hasStatusLobby() || $this->hasStatusFinished()));
    }

    /**
     * @return Collection
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function loadUsers()
    {
        $this->load('users');
    }

    public function loadGameRoomDominoes()
    {
        $this->load('gameRoomDominoes');
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return GameRoomUser|null
     */
    public function getGameRoomUser(int $id): ?GameRoomUser
    {
        return $this->gameRoomUsers()->whereUserId($id)->first();
    }

    /**
     * @return HasMany
     */
    public function gameRoomUsers(): HasMany
    {
        return $this->hasMany(GameRoomUser::class);
    }

    /**
     * @return Collection
     */
    public function getGameRoomUsers(): Collection
    {
        return $this->gameRoomUsers;
    }

    public function getBiggestOwnedDoubleDomino()
    {
        return GameRoomDomino::getBiggestOwnedDoubleDomino($this->id);
    }

    /**
     * @return HasMany
     */
    public function gameRoomDominoes(): HasMany
    {
        return $this->hasMany(GameRoomDomino::class)->orderBy('id');
    }

    /**
     * @return GameRoom|null
     */
    public function getGameRoom(): ?GameRoom
    {
        return $this->gameRoom;
    }

    /**
     * @return Collection
     */
    private function getPlacedDominoes(): Collection
    {
        return $this->gameRoomDominoes()->placed()->get()->sortBy('index_position');
    }

    /**
     * @return bool
     */
    public function hasPlacedDominoes(): bool
    {
        return $this->gameRoomDominoes()->placed()->exists();
    }

    /**
     * @return GameRoomDomino|null
     */
    public function getFirstPlacedDomino(): ?GameRoomDomino
    {
        return $this->getPlacedDominoes()->first();
    }

    /**
     * @return GameRoomDomino|null
     */
    public function getLastPlacedDomino(): ?GameRoomDomino
    {
        return $this->getPlacedDominoes()->last();
    }

    /**
     * @return GameRoomDomino[]
     */
    public function getGameRoomDominoes()
    {
        return $this->gameRoomDominoes;
    }

    public function hasExtraDominoesLeft()
    {
        return $this->gameRoomDominoes()->extra()->exists();
    }

    /**
     * @param int $id
     *
     * @return GameRoomDomino|null
     */
    public function getGameRoomDomino(int $id): ?GameRoomDomino
    {
        return $this->gameRoomDominoes()->whereId($id)->first();
    }

    /**
     * @return GameRoomUser|null
     */
    public function getNextGameRoomUser(): ?GameRoomUser
    {
        $currentUser = $this->gameRoomUsers()->whereUserId($this->user_id)->first();
        $nextIndex = $currentUser->index + 1;

        return $this->gameRoomUsers()->whereIndex($nextIndex)->first();
    }

    /**
     * @return string
     */
    public function getWinnersName(): string
    {
        $usersDominoesSum = [];

        $users = $this->getUsers();
        foreach ($users as $user) {
            $usersDominoesSum[$user->getDominoesSum()][] = $user->getName();
        }

        $sums = array_keys($usersDominoesSum);
        $minSum = min($sums);

        return implode($usersDominoesSum[$minSum], ', ');
    }

    /**
     * @return GameRoomUser|null
     */
    public function getFirstGameRoomUser(): ?GameRoomUser
    {
        return $this->gameRoomUsers()->whereIndex(1)->first();
    }

    /**
     * @param User $user
     *
     * @return void
     */
    public function addUser(User $user)
    {
        $gameRoomUser = new GameRoomUser();
        $gameRoomUser->gameRoom()->associate($this);
        $gameRoomUser->user()->associate($user);
        $gameRoomUser->save();
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isUserTurn(User $user): bool
    {
        return $this->user_id == $user->id;
    }

    /**
     * @param User $user
     *
     * @return void
     */
    public function setUserTurn(User $user)
    {
        $this->user()->associate($user);
    }

    /**
     * @return bool
     */
    public function maximumNumberOfPlayers(): bool
    {
        return $this->users()->count() >= 4;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function hasUser(User $user): bool
    {
        return $this->users->contains($user);
    }

    /**
     * @return bool
     */
    public function noMinimumPlayers(): bool
    {
        return $this->users()->count() < 2;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatusLobby()
    {
        $this->status = self::STATUS_LOBBY;
    }

    /**
     * @return bool
     */
    public function hasStatusLobby(): bool
    {
        return $this->status === self::STATUS_LOBBY;
    }

    public function setStatusSelection()
    {
        $this->status = self::STATUS_SELECTION;
    }

    /**
     * @return bool
     */
    public function hasStatusSelection(): bool
    {
        return $this->status === self::STATUS_SELECTION;
    }

    public function reset()
    {
        $this->gameRoomDominoes()->each(function ($gameRoomDomino) {
            $gameRoomDomino->delete();
        });
    }

    public function setStatusStarted()
    {
        $this->status = self::STATUS_STARTED;
    }

    /**
     * @return bool
     */
    public function hasStatusStarted(): bool
    {
        return $this->status === self::STATUS_STARTED;
    }

    public function setStatusFinished()
    {
        $this->status = self::STATUS_FINISHED;
    }

    /**
     * @return bool
     */
    public function hasStatusFinished(): bool
    {
        return $this->status === self::STATUS_FINISHED;
    }

    /**
     * @return string
     */
    public function getStatusNameAttribute(): string
    {
        return self::STATUS_NAME[$this->status];
    }
}
