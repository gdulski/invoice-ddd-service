<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Src\Presentation\Controllers\HealthController;

Route::get('/health', [HealthController::class, 'check']);
