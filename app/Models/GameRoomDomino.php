<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\GameRoomDomino
 *
 * @property int $id
 * @property int $game_room_id
 * @property int $domino_id
 * @property int|null $user_id
 * @property int|null $index_position
 * @property int $flip
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Domino $domino
 * @property-read \App\Models\GameRoom $gameRoom
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomDomino newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomDomino newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomDomino query()
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomDomino whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomDomino whereDominoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomDomino whereFlip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomDomino whereGameRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomDomino whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomDomino whereIndexPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomDomino whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GameRoomDomino whereUserId($value)
 * @mixin \Eloquent
 * @property-read bool $placed
 * @property-read bool $selected
 * @property-read int $side1
 * @property-read int $side2
 * @method static Builder|GameRoomDomino gameRoomId(int $value)
 * @method static Builder|GameRoomDomino inDominoId(array $values)
 * @method static Builder|GameRoomDomino placed()
 * @method static Builder|GameRoomDomino selected()
 * @method static Builder|GameRoomDomino extra()
 * @method static Builder|GameRoomDomino notPlaced()
 */
class GameRoomDomino extends Model
{
    use HasFactory;

    protected $table = 'game_room_domino';

    protected $fillable = [
        'selected'
    ];

    protected $appends = [
        'side1', 'side2', 'selected', 'placed'
    ];

    /**
     * @return int
     */
    public function getSum(): int
    {
        return $this->side1 + $this->side2;
    }

    /**
     * @return BelongsTo
     */
    public function gameRoom(): BelongsTo
    {
        return $this->belongsTo(GameRoom::class);
    }

    /**
     * @param GameRoom $gameRoom
     *
     * @return void
     */
    public function setGameRoom(GameRoom $gameRoom)
    {
        $this->gameRoom()->associate($gameRoom);
    }

    /**
     * @return BelongsTo
     */
    public function domino(): BelongsTo
    {
        return $this->belongsTo(Domino::class);
    }

    /**
     * @param Domino $domino
     *
     * @return void
     */
    public function setDomino(Domino $domino)
    {
        $this->domino()->associate($domino);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param User $user
     *
     * @return void
     */
    public function setUser(User $user)
    {
        $this->user()->associate($user);
    }

    /**
     * @param Builder $query
     * @param int $value
     *
     * @return Builder
     */
    public function scopeGameRoomId(Builder $query, int $value): Builder
    {
        return $query->where('game_room_id', $value);
    }

    /**
     * @param Builder $query
     * @param int[] $values
     *
     * @return Builder
     */
    public function scopeInDominoId(Builder $query, array $values): Builder
    {
        return $query->whereIn('domino_id', $values);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeSelected(Builder $query): Builder
    {
        return $query->whereNotNull('user_id');
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePlaced(Builder $query): Builder
    {
        return $query->whereNotNull('index_position');
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeNotPlaced(Builder $query): Builder
    {
        return $query->whereNull('index_position');
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeExtra(Builder $query): Builder
    {
        return $query->whereNull('user_id')->whereNull('index_position');
    }

    /**
     * @param int $gameRoomId
     *
     * @return GameRoomDomino|null
     */
    public static function getBiggestOwnedDoubleDomino(int $gameRoomId): ?GameRoomDomino
    {
        $dominoesId = Domino::getDoubledDominoes()->pluck('id')->toArray();

        return self::gameRoomId($gameRoomId)->InDominoId($dominoesId)->selected()->orderBy('domino_id', 'desc')->get()->first();
    }

    /**
     * @return int
     */
    public function getSide1(): int
    {
        return $this->side1;
    }

    /**
     * @return int
     */
    public function getSide2(): int
    {
        return $this->side2;
    }

    /**
     * @return bool
     */
    public function isDouble(): bool
    {
        return $this->side1 === $this->side2;
    }

    /**
     * @return int
     */
    public function getSide1Attribute(): int
    {
        return $this->domino->side1;
    }

    /**
     * @return int
     */
    public function getSide2Attribute(): int
    {
        return $this->domino->side2;
    }

    /**
     * @return int
     */
    public function getLeftSide(): int
    {
        return (!$this->isFlipped()) ? $this->getSide1() : $this->getSide2();
    }

    /**
     * @return int
     */
    public function getRightSide(): int
    {
        return (!$this->isFlipped()) ? $this->getSide2() : $this->getSide1();
    }

    /**
     * @param int $value
     *
     * @return bool
     */
    public function matchesSide(int $value): bool
    {
        return (($this->getSide1() === $value) || ($this->getSide2() === $value));
    }

    public function setFlipped()
    {
        $this->flip = true;
    }

    /**
     * @return bool
     */
    public function isFlipped(): bool
    {
        return $this->flip;
    }

    /**
     * @param int $value
     *
     * @return void
     */
    public function setIndexPosition(int $value)
    {
        $this->index_position = $value;
    }

    /**
     * @return int|null
     */
    public function getIndexPosition(): ?int
    {
        return $this->index_position;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function getSelectedAttribute(): bool
    {
        return $this->user_id != null;
    }

    /**
     * @return bool
     */
    public function isSelected(): bool
    {
        return $this->selected;
    }

    /**
     * @return bool
     */
    public function getPlacedAttribute(): bool
    {
        return $this->index_position !== null;
    }

    /**
     * @return bool
     */
    public function isPlaced(): bool
    {
        return $this->placed;
    }

    /**
     * @param $user
     *
     * @return bool
     */
    public function isOwner($user): bool
    {
        return $this->user_id == $user->id;
    }
}
