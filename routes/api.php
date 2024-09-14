<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/passkeys/register', [\App\Http\Controllers\Api\PasskeyController::class, 'registerOptions'])->middleware('auth:sanctum');
