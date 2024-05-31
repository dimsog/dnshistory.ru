<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property int $zone_id
 */
final class Domain extends Model
{
    public $timestamps = false;

    protected $table = 'domains';
}
