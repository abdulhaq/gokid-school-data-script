<?php

use App\Http\Controllers\MergingData;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/testCsv', [MergingData::class, 'getCurrentLive'])->name('getCurrentLive');
Route::get('/getNewData/{school_id}', [MergingData::class, 'getNewData'])->name('getNewData');
Route::get('/updateData/{school_id}', [MergingData::class, 'updateData'])->name('updateData');
Route::get('/inDbNotInExcel/{school_id}', [MergingData::class, 'inDbNotInExcel'])->name('inDbNotInExcel');
Route::get('/familiesWithSameAddress/{school_id}', [MergingData::class, 'familiesWithSameAddress'])->name('familiesWithSameAddress');
Route::get('/linkMembersWithUsers/{school_id}', [MergingData::class, 'linkMembersWithUsers'])->name('linkMembersWithUsers');
