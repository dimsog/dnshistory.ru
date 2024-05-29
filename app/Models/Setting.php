<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting
{
    public static function getYandexMetrika(): ?string
    {
        return config('app.yandex_metrika');
    }
}
