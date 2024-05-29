<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Api\V2\DnsController;

Route::get('/', [HomeController::class, 'index']);
Route::post('/api/v2/dns/read', DnsController::class);
