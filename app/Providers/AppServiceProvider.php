<?php

namespace App\Providers;

use App\Model\Task;
use App\SiteSetting;
use App\Model\ThirdPartyPatientMaster;
use Illuminate\Support\Facades\DB;
use URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\Paginator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        if(config('app.env') === 'production') {
            \URL::forceScheme('https');
        }
        //
        if (class_exists('Swift_Preferences')) {
            \Swift_Preferences::getInstance()->setTempDir(storage_path().'/tmp');
        } else {
            // \Log::warning('Class Swift_Preferences does not exists');
        }
        view()->composer('*', function ($view) {
            $taskList=[];
            if(auth()->check()){
                $userId = auth()->user()->id;
                $cacheKey = 'task_list_user_' . $userId; 
                $userDepartments = DB::table('department_user')->where('user_id', auth()->id())->where('del_flag','N')->pluck('department_id');
                $taskList = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($userDepartments) {
                    return Task::select('id')->where('del_flag', 'N')->where('task_status', 'Pending')
                    ->where(function ($q) use ($userDepartments) {
                            $q->whereIn('department_id', $userDepartments)
                            ->orWhere(function ($q2) {
                                $q2->where('assign_id', auth()->id())
                                    ->orWhere('created_by', auth()->id());
                            });
                        })
                    ->whereDate('task_master.due_date','<',date('Y-m-d'))
                    ->get();
                   
                });
                $siteSettings = SiteSetting::where('del_flag', 'N')->first();
            }
            $view->with([
                'taskList' => count($taskList),
                'announcementPopupEnabled' =>
                $siteSettings->announcement_popup_enabled ?? 1
            ]);
        });

        view()->composer('*', function ($view) {
           //$pendingCount = ThirdPartyPatientMaster::select(DB::raw('count(id) as count'))->whereNull('patient_id')->where('deleted_flag','N')->get();

            $view->with('pendingCount',0);
        });
    //    URL::forceScheme('https');
    }
}
