<?php

namespace App\Console;

use App\Helpers\HHACaregiversHelper;
use App\Helpers\HHAPatientHelper;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DB;
use Mail;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

      $schedule->call(function () {
        app('App\Http\Controllers\HHACaregiversController')->agencyWiseSYNCCaregiver();

      })->everyThirtyMinutes();

      $schedule->call(function () {

        app('App\Http\Controllers\HHACaregiversController')->updateCaregiverDemographics();
        app('App\Http\Controllers\HHAPatientController')->agencyWisePatientDemographicDetails();
      })->everyTenMinutes();


      $schedule->call(function () {
       app('App\Http\Controllers\HHACaregiversController')->agencyWiseSYNCMedical();

      })->everyThirtyMinutes();

      $schedule->call(function () {

         app('App\Http\Controllers\HHACaregiversController')->caregiverSyncOtherCompliance();
       })->everyFifteenMinutes();
      $schedule->call(function () {
        app('App\Http\Controllers\HHAPatientController')->agencyWiseSYNCPatient();

      })->everyThirtyMinutes();

      $schedule->call(function () {
        app('App\Http\Controllers\HHACaregiversController')->caregiverModifiedCaregiverIds();
        app('App\Http\Controllers\HHAPatientController')->patientModifiedPatientIds();
      })->everyTwoHours();

      $schedule->call(function () {
        app('App\Http\Controllers\HHACaregiversController')->checkForAllCaregiverSYNCOrNot();
      })->dailyAt('00:00');

      $schedule->call(function () {
        info("Kernal");
        app('App\Http\Controllers\AlayacareCronJobController')->autoDueSkills();

      })->everyThirtyMinutes();
      $schedule->call(function () {

        app('App\Http\Controllers\AlayacareCronJobController')->updateEmployeeDemographic();
      })->everyTenMinutes();

      $schedule->call(function () {
          app('App\Http\Controllers\CronjobEventStatusUpdateController')->updateSMSStatus();
        })->hourly();

        // Daily Referral Email Scheduling
        $schedule->command('daily-referral:send-scheduled')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();

        // Process Agency Merge Data - runs every 10 minutes
        $schedule->command('process-agency-data')
            ->everyTenMinutes()
            ->withoutOverlapping()
            ->runInBackground();

        // AI reminder calls — fires hourly, command self-guards to 10am–7pm window
        $schedule->command('ai-calls:send-reminders')
            ->hourly()
            ->withoutOverlapping()
            ->runInBackground();

        // Validate Import Records - runs every 5 minutes
        // $schedule->command('import:validate-records')
        //     ->everyFiveMinutes()
        //     ->withoutOverlapping()
        //     ->runInBackground();

        // $schedule->call(function () {
        //          app('App\Http\Controllers\RobortCronjobController')->syncRemoteAgencyPatient();


        //       })->dailyAt('01:00');

        // $schedule->call(function () {
        //   app('App\Http\Controllers\CronjobEventStatusUpdateController')->updateStatus();
        // })->dailyAt('00:00');


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
