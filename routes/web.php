<?php

use App\Http\Controllers\GasController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { 
    return view('welcome'); 
})->name('welcome');

Route::get('/dashboard', [GasController::class, 'index'])->name('dashboard');

// Chart endpoints
Route::get('/chart-data', [GasController::class, 'chart'])->name('chart.data');

// Top 5 filtered data
Route::get('/top-data', [GasController::class, 'topData'])->name('top.data');

// Trend Analysis
Route::get('/dashboard/trend-analysis', [GasController::class, 'trendAnalysis'])->name('trend.analysis');

// Comparison between shippers
Route::get('/comparison-data', [GasController::class, 'comparisonData'])->name('comparison.data');