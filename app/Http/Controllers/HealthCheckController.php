<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\SiteSetting;

class HealthCheckController extends Controller
{
    /**
     * Health check endpoint
     * Returns 200 with "okay" if enabled, 500 with "error" if disabled
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get setting from database
        $siteSetting = SiteSetting::where('del_flag', 'N')->first();

        // Check if health check is enabled (default to true if not found)
        $isEnabled = $siteSetting ? (bool)$siteSetting->health_check_enabled : true;

        if ($isEnabled) {
            return response('okay', 200)
                ->header('Content-Type', 'text/plain');
        } else {
            return response('error', 500)
                ->header('Content-Type', 'text/plain');
        }
    }
}
