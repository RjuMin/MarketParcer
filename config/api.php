<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParserController;

Route::post('/parse', [ParserController::class, 'parseProduct']);