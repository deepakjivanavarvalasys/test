<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DailyReportLog;
use App\Models\Module;
use App\Models\User;
use App\Providers\RouteServiceProvider;

use App\Repository\DailyReportLog\DailyReportLogRepository;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(DailyReportLogRepository $DailyReportLogRepository)
    {
        $this->DailyReportLogRepository=$DailyReportLogRepository;
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if(User::whereEmail($credentials['email'])->whereStatus('1')->exists()) {
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $user->logged_on = date('Y-m-d H:i:s');
                $user->remote_address = $request->getClientIp();
                $user->save();

                if($request->has('remember_me')) {
                    Cookie::queue(Cookie::forever('login_email', $credentials['email']));
                    Cookie::queue(Cookie::forever('login_password', $credentials['password']));
                }else {
                    Cookie::queue(Cookie::forget('login_email'));
                    Cookie::queue(Cookie::forget('login_password'));
                }

                $module = Module::whereRoleId($user->role_id)->whereStatus('1')->first();
                if(!empty($module)) {

                    //Add Sign In entry in daily report log
                    $resultDailyReportLog = DailyReportLog::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();

                    if(!empty($resultDailyReportLog)) {
                        //get start date using sign in date
                        $sign_in = $resultDailyReportLog->sign_in;
                        $time = date('H', strtotime($sign_in));
                        if($time >= 12 && $time <= 23) {
                            $shift_start = date('Y-m-d 12:00:00', strtotime($sign_in));
                            $shift_end = date('Y-m-d 11:59:59',strtotime ( '+1 day', strtotime($sign_in)));
                        } else {
                            $shift_start = date('Y-m-d 12:00:00',strtotime ( '-1 day', strtotime($sign_in)));
                            $shift_end = date('Y-m-d 11:59:59',strtotime($sign_in));
                        }

                        $date = date('Y-m-d H:i:s');
                        if($date >= $shift_start && $date <= $shift_end) {
                            $this->DailyReportLogRepository->update($resultDailyReportLog->id, array(
                                'sign_out' => date('Y-m-d H:i:s'),
                                'remote_address' => $request->getClientIp()
                            ));
                        } else {
                            DailyReportLogRepository::store(array(
                                'user_id' => $user->id,
                                'sign_in' => date('Y-m-d H:i:s'),
                                'remote_address' => $request->getClientIp()
                            ));

                            //Update Sign Out if missing
                            if(empty($resultDailyReportLog->sign_out)) {
                                DailyReportLogRepository::update($resultDailyReportLog->id, array(
                                    'sign_out' => $shift_end,
                                ));
                            }
                        }
                    } else{
                        DailyReportLogRepository::store(array(
                            'user_id' => $user->id,
                            'sign_in' => date('Y-m-d H:i:s'),
                            'remote_address' => $request->getClientIp()
                        ));
                    }

                    return redirect()->route($module->route_name);
                } else {
                    return redirect()->route('logout');
                }

            } else {
                return back()->withInput()->with('error', 'Invalid Credentials');
            }
        } else {
            return back()->withInput()->with('error', 'Account suspended, contact admin.');
        }

    }

}
