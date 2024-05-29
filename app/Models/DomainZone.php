<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 */
class DomainZone extends Model
{
    public $timestamps = false;

    public $incrementing = false;


    /**
     * @return Collection<DomainZone>
     */
    public static function findAll(): Collection
    {
        return self::query()->get();
    }
}
