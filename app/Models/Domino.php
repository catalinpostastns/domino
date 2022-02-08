<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * App\Models\Domino
 *
 * @property int $id
 * @property int $side1
 * @property int $side2
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Domino newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Domino newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Domino query()
 * @method static \Illuminate\Database\Eloquent\Builder|Domino whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domino whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domino whereSide1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domino whereSide2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Domino whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static Builder|Domino side1(int $value)
 * @method static Builder|Domino side2(int $value)
 */
class Domino extends Model
{
    use HasFactory;

    protected $fillable = [
        'side1', 'side2',
    ];

    /**
     * @param Builder $query
     * @param int $value
     *
     * @return Builder
     */
    public function scopeSide1(Builder $query, int $value): Builder
    {
        return $query->where('side1', $value);
    }

    /**
     * @param Builder $query
     * @param int $value
     *
     * @return Builder
     */
    public function scopeSide2(Builder $query, int $value): Builder
    {
        return $query->where('side2', $value);
    }

    /**
     * @return Collection
     */
    public static function getDoubledDominoes(): Collection
    {
        $dominoes = [];

        for ($i = 0; $i <= 6; $i++) {
            $dominoes[] = Domino::side1($i)->side2($i)->first();
        }

        return collect($dominoes);
    }
}
