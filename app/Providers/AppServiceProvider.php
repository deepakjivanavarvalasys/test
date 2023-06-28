<?php

namespace App\Providers;

use App\Models\EMENotification;
use App\Models\ManagerNotification;
use App\Models\Module;
use App\Models\QANotification;
use App\Models\QATLNotification;
use App\Models\RANotification;
use App\Models\RATLNotification;
use App\Models\VMNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

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
        view()->composer('*', function ($view) {

            if(Auth::check()) {
                $module = Module::whereRoleId(Auth::user()->role_id)->first();

                $notifications = null;
                switch ($module->slug) {
                    case 'admin': break;
                    case 'manager':
                        $notifications = ManagerNotification::where('recipient_id', Auth::id())->where('read_status', 0)->orderBy('created_at', 'DESC')->get();
                        break;
                    case 'team_leader':
                        $notifications = RATLNotification::where('recipient_id', Auth::id())->where('read_status', 0)->orderBy('created_at', 'DESC')->get();
                        break;
                    case 'agent':
                        $notifications = RANotification::where('recipient_id', Auth::id())->where('read_status', 0)->orderBy('created_at', 'DESC')->get();
                        break;
                    case 'qa_team_leader':
                        $notifications = QATLNotification::where('recipient_id', Auth::id())->where('read_status', 0)->orderBy('created_at', 'DESC')->get();
                        break;
                    case 'quality_analyst':
                        $notifications = QANotification::where('recipient_id', Auth::id())->where('read_status', 0)->orderBy('created_at', 'DESC')->get();
                        break;
                    case 'email_marketing_executive':
                        $notifications = EMENotification::where('recipient_id', Auth::id())->where('read_status', 0)->orderBy('created_at', 'DESC')->get();
                        break;
                    case 'vendor_manager':
                        $notifications = VMNotification::where('recipient_id', Auth::id())->where('read_status', 0)->orderBy('created_at', 'DESC')->get();
                        break;
                }
                $view->with('notifications', $notifications);
            }

        });
    }
}
