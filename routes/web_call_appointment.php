<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CallAppointmentController;
use App\Http\Controllers\AiCallLogController;

Route::group(['middleware' => ['XSS']], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/call-appointments', [CallAppointmentController::class, 'index']);
        Route::get('/call-appointments-ajax-list', [CallAppointmentController::class, 'ajaxList']);
        Route::post('/call-appointments/make-call', [CallAppointmentController::class, 'makeCall']);
        Route::post('/call-appointments/make-direct-call', [CallAppointmentController::class, 'makeDirectCall']);
        Route::get('/call-appointments/logs', [CallAppointmentController::class, 'callLogs']);
        Route::get('/call-appointments/logs-ajax-list', [CallAppointmentController::class, 'callLogsAjaxList']);

        // AI Call Logs Admin Panel — static routes MUST come before {id} wildcard
        Route::get('/ai-call-logs', [AiCallLogController::class, 'index']);
        Route::get('/ai-call-logs/ajax-list', [AiCallLogController::class, 'ajaxList']);
        Route::get('/ai-call-logs/booking/{id}', [AiCallLogController::class, 'bookingDetail']);
        Route::get('/ai-call-logs/{id}', [AiCallLogController::class, 'detail']);
        Route::get('/ai-call-logs/{id}/fetch-conversation', [AiCallLogController::class, 'fetchConversation']);
        Route::get('/ai-call-logs/{id}/audio', [AiCallLogController::class, 'getAudio']);
        Route::post('/ai-call-logs/{id}/verify', [AiCallLogController::class, 'verify']);
        Route::post('/ai-call-logs/{id}/convert', [AiCallLogController::class, 'convertToAppointment']);
        Route::post('/ai-call-logs/{id}/reminder', [AiCallLogController::class, 'sendReminder']);
        Route::post('/ai-call-logs/{id}/notes', [AiCallLogController::class, 'saveNotes']);
        Route::post('/ai-call-logs/{id}/fire-call', [AiCallLogController::class, 'fireCall']);
        Route::post('/ai-call-logs/{id}/reminder-call', [AiCallLogController::class, 'sendReminderCall']);
        Route::post('/ai-call-logs/{id}/update-booking', [AiCallLogController::class, 'updateBooking']);
        Route::get('/patient/{patientId}/ai-call-logs', [AiCallLogController::class, 'patientCallLogs']);
        Route::post('/patient/{patientId}/ai-call-logs/add-call', [AiCallLogController::class, 'addManualCall']);
        Route::get('/ai-call-logs/{id}/reminder-audio', [AiCallLogController::class, 'getReminderAudio']);
        Route::get('/ai-call-logs/{id}/fetch-reminder-conversation', [AiCallLogController::class, 'fetchReminderConversation']);
        Route::get('/ai-call-logs/attempt/{attemptId}/audio', [AiCallLogController::class, 'getAttemptAudio']);
        Route::get('/ai-call-logs/attempt/{attemptId}/fetch-transcript', [AiCallLogController::class, 'fetchAttemptTranscript']);
    });
});
