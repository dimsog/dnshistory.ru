<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $domain_id
 * @property int $date
 * @property string $hash
 */
final class Dns extends Model
{
    public $timestamps = false;

    protected $table = 'dns';
}
