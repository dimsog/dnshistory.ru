<?php

namespace App\View\Components;

use App\Models\Setting;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Layout extends Component
{
    public function render(): View|Closure|string
    {
        return view('components.layout', [
            'yandex_metrika' => Setting::getYandexMetrika()
        ]);
    }
}
