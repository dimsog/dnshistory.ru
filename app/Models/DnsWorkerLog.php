<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $date
 * @property string $server
 * @property string $key
 * @property string $errors
 */
class DnsWorkerLog extends Model
{
    use HasFactory;

    public const NETWORK_ERROR = 'network_error';

    public const BAD_RESPONSE_ERROR = 'bad_response_error';

    public $timestamps = false;

    protected $fillable = [
        'date',
        'server',
        'key',
        'errors',
    ];
}
