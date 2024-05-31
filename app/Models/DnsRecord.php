<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $dns_id
 * @property string $type
 * @property string $class
 * @property string $value
 */
final class DnsRecord extends Model
{
    public $timestamps = false;

    protected $table = 'dns_records';
}
