<?php

use App\Http\Controllers\MergingData;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/testCsv', [MergingData::class, 'getCurrentLive'])->name('getCurrentLive');
Route::get('/getNewData', [MergingData::class, 'getNewData'])->name('getNewData');
Route::get('/updateData', [MergingData::class, 'updateData'])->name('updateData');
Route::get('/inDbNotInExcel', [MergingData::class, 'inDbNotInExcel'])->name('inDbNotInExcel');