<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgencyFileController;
use App\Http\Controllers\AgencyController;

/*
|--------------------------------------------------------------------------
| Agency File Manager Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => ['auth'], 'prefix' => 'file-manager'], function () {
    // Global all-agencies file listing (admin only)
    Route::get('/all-files', [AgencyFileController::class, 'allAgenciesFiles'])->name('file-manager.all-files');
    Route::get('/all-files/data', [AgencyFileController::class, 'allAgenciesFilesData'])->name('file-manager.all-files.data');
    Route::get('/all-files/archived', [AgencyFileController::class, 'allAgenciesArchivedData'])->name('file-manager.all-files.archived');
    Route::post('/all-files/bulk-download', [AgencyFileController::class, 'bulkDownload'])->name('file-manager.all-files.bulk-download');
    Route::get('/all-files/export', [AgencyFileController::class, 'exportCsv'])->name('file-manager.all-files.export');

    // File Manager Page
    Route::get('/', [AgencyFileController::class, 'index'])->name('agency.file-manager');

    // Folder operations
    Route::post('/folder/create', [AgencyFileController::class, 'createFolder']);
    Route::get('/folder/tree', [AgencyFileController::class, 'folderTree']);
    Route::delete('/folder/{id}', [AgencyFileController::class, 'deleteFolder']);

    // File operations
    Route::post('/file/upload', [AgencyFileController::class, 'uploadFile']);
    Route::get('/files', [AgencyFileController::class, 'listFiles']);
    Route::get('/files/all', [AgencyFileController::class, 'listAllFiles']);
    Route::post('/files/bulk-download', [AgencyFileController::class, 'bulkDownloadAgency']);
    Route::delete('/file/{id}', [AgencyFileController::class, 'deleteFile']);
    Route::put('/file/rename', [AgencyFileController::class, 'rename']);
    Route::get('/file/download/{id}', [AgencyFileController::class, 'downloadFile']);
    Route::get('/file/preview/{id}', [AgencyFileController::class, 'previewFile']);

    // Archive & Restore
    Route::get('/file/archived', [AgencyFileController::class, 'archivedList']);
    Route::get('/folder/archived/{id}', [AgencyFileController::class, 'archivedFolderContents']);
    Route::post('/file/restore/{id}', [AgencyFileController::class, 'restoreFile']);
    Route::post('/folder/restore/{id}', [AgencyFileController::class, 'restoreFolder']);

    // Patient File Links
    Route::post('/file/{id}/link-patient', [AgencyFileController::class, 'linkPatient']);
    Route::delete('/file/{id}/unlink-patient/{patientId}', [AgencyFileController::class, 'unlinkPatient']);
    Route::get('/file/{id}/linked-patients', [AgencyFileController::class, 'getLinkedPatients']);
    Route::get('/patient/{patientId}/files', [AgencyFileController::class, 'getPatientFiles']);
    Route::post('/file/{id}/add-to-chart', [AgencyFileController::class, 'addToChart']);
});

Route::group(['middleware' => ['auth']], function () {
    // Toggle File Manager for agency (stays under /agency prefix as used in blade JS variable)
    Route::post('/agency/toggle-file-manager', [AgencyController::class, 'statusChangeFileManager']);
});
