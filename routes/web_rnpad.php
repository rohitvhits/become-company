<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RNPadRecordController;

Route::group(['middleware' => 'auth'], function () {
    Route::controller(RNPadRecordController::class)
			->prefix('rnpad')
			->name('rnpad.')
			->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('document-list', 'index')->name('documentList');
                Route::get('document-ajax', 'documentAjax')->name('documentAjax');
                Route::get('document-export-csv', 'exportCsv')->name('exportCsv');
                Route::get('rnpad-services-list','rnpadServicesList');
				Route::get('data/list', 'getData')->name('data');
				Route::post('send-rnpad-document', 'saveRNPad')->name('saveRNPad');
			});
});