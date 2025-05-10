<?php

use App\Http\Controllers\API\KendaraanController;
use Illuminate\Support\Facades\Route;

Route::get('/pajak/kendaraan', [KendaraanController::class, 'getDataKendaraan']);
