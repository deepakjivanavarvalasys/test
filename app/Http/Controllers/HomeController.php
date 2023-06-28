<?php

namespace App\Http\Controllers;

use App\Models\DailyReportLog;
use App\Repository\DailyReportLog\DailyReportLogRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct(DailyReportLogRepository $DailyReportLogRepository)
    {
        $this->DailyReportLogRepository=$DailyReportLogRepository;
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->logged_on = null;
        $user->save();

        $resultDailyReportLog = DailyReportLog::where('user_id', $user->id)->orderBy('created_at', 'DESC')->first();
        $this->DailyReportLogRepository->update($resultDailyReportLog->id, array(
            'sign_out' => date('Y-m-d H:i:s'),
            'remote_address' => $request->getClientIp()
        ));

        Auth::logout();
        return redirect()->route('login');
    }

    public function lockscreen()
    {
        // TODO: time tracking code goes here...

        $user = Auth::user();
        $email_id = $user->email;
        $user->logged_on = null;
        $user->save();

        return view('lockscreen', ['email' => $email_id]);
    }

    public function unlockscreen()
    {
        // TODO: time tracking code goes here...

        $user = Auth::user();
        $user->logged_on = date('Y-m-d H:i:s');
        $user->save();

        $module = \App\Models\Module::whereRoleId(Auth::user()->role_id)->first();

        return redirect()->route($module->route_name);
    }

}
