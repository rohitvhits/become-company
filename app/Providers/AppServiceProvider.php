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
use App\Model\DomainConfig;
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
            $host = request()->getHost();

            $default = [
                'logo'        => env('APP_LOGO', 'img/logo.png'),
                'favicon'     => 'img/favicon.png',
                'title'       => env('APP_NAME', 'Laravel'),
                'login_bg'    => '#0F0D0B',
                'theme_color' => '#0F0D0B',
                'logo_style'  => 'width:100%;',
                'login_image' => 'img/pana.png',
            ];

            $config = $default;

            try {
                $dbConfig = DomainConfig::where('domain', $host)->first();
                if ($dbConfig) {
                    $config = [
                        'logo'        => $dbConfig->logo        ?: $default['logo'],
                        'favicon'     => $dbConfig->favicon     ?: $default['favicon'],
                        'title'       => $dbConfig->title       ?: $default['title'],
                        'login_bg'    => $dbConfig->login_bg    ?: $default['login_bg'],
                        'theme_color' => $dbConfig->theme_color ?: $default['theme_color'],
                        'logo_style'  => $dbConfig->logo_style  ?: $default['logo_style'],
                        'login_image' => $dbConfig->login_image ?: $default['login_image'],
                    ];
                }
            } catch (\Exception $e) {
                // table may not exist yet during migrations
            }

            $view->with([
                'appLogo'        => $config['logo'],
                'appFavicon'     => $config['favicon'],
                'appTitle'       => $config['title'],
                'appLoginBg'     => $config['login_bg'],
                'appThemeColor'  => $config['theme_color'],
                'appLogoStyle'   => $config['logo_style'],
                'appLoginImage'  => $config['login_image'],
            ]);
        });

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
