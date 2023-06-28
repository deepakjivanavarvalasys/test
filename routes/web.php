<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    //return view('welcome');
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::middleware(['guest'])->group(function (){
    Route::view('/login', 'login')->name('login');
    Route::get('/forgot-password', [App\Http\Controllers\GuestController::class, 'forgot_password'])->name('forgot_password');
    Route::post('/send-reset-link', [App\Http\Controllers\GuestController::class, 'send_reset_link'])->name('send_reset_link');
    Route::any('/reset-password/{token}', [App\Http\Controllers\GuestController::class, 'reset_password'])->name('reset_password');
    Route::get('/create-new-password/{token}', [App\Http\Controllers\GuestController::class, 'create_new_password'])->name('create_new_password');
    Route::post('/store-new-password', [App\Http\Controllers\GuestController::class, 'store_new_password'])->name('store_new_password');


    Route::get('/excel-to-array', [App\Http\Controllers\GuestController::class, 'excelToArray'])->name('excelToArray');
    Route::post('/convert-excel-to-array', [App\Http\Controllers\GuestController::class, 'convertExcelToArray'])->name('convertExcelToArray');

    Route::any('/extract-number', [App\Http\Controllers\GuestController::class, 'extractNumber'])->name('extract_number');
    Route::any('/array-to-object', [App\Http\Controllers\GuestController::class, 'arrayToObject'])->name('array_to_object');
});

Route::any('/logout', [App\Http\Controllers\HomeController::class, 'logout'])->name('logout');
Route::any('/lockscreen', [App\Http\Controllers\HomeController::class, 'lockscreen'])->name('lockscreen');
Route::any('/unlockscreen', [App\Http\Controllers\HomeController::class, 'unlockscreen'])->name('unlockscreen');


Route::prefix('admin')->middleware(['web', 'check.admin'])->name('admin.')->group(function (){

    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/test', [App\Http\Controllers\Admin\DashboardController::class, 'test'])->name('test');

    //User Management Routes
    include('admin/user_routes.php');

    //Role Management Routes
    include('admin/role_routes.php');
    //Department Management Routes
    include('admin/department_routes.php');
    //Designation Management Routes
    include('admin/designation_routes.php');

    //Campaign Settings Routes
    include('admin/campaign_filter_routes.php');
    include('admin/campaign_type_routes.php');
    include('admin/campaign_status_routes.php');
    include('admin/agent_work_type_routes.php');

    //Geo Management Routes
    include('admin/geo_routes.php');

    //Site Setting Routes
    include('admin/site_setting_routes.php');
    include('admin/tutorial_routes.php');
    include('admin/cron_trigger.php');

    //Holiday Management Routes
    include('admin/holiday_routes.php');
});

Route::prefix('manager')->middleware(['web', 'check.manager'])->name('manager.')->group(function (){

    Route::get('/dashboard', [App\Http\Controllers\Manager\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports',[App\Http\Controllers\Manager\ReportsController::class,'index'])->name('reports');
    Route::get('/reportshow', [App\Http\Controllers\Manager\ReportsController::class, 'selectdate']);
    Route::post('/reportshow', [App\Http\Controllers\Manager\ReportsController::class, 'selectdate']);
    

    //User Routes
    include('manager/user_routes.php');

    //Dashboard Routes
    include('manager/dashboard_routes.php');

    //Campaign Management Routes
    include('manager/campaign_routes.php');

    //Campaign Assign Routes
    include('manager/campaign_assign_routes.php');

    //Holiday Routes
    include('manager/holiday_routes.php');

    //Data Routes
    include('manager/data_routes.php');

    //Campaign Issue Routes
    include('manager/campaign_issue_routes.php');

});

Route::prefix('team-leader')->middleware(['web', 'check.team_leader'])->name('team_leader.')->group(function (){

    Route::get('/dashboard', [App\Http\Controllers\TeamLeader\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [App\Http\Controllers\TeamLeader\ReportsController::class, 'index'])->name('reports');
    Route::post('/reportshow', [App\Http\Controllers\TeamLeader\ReportsController::class, 'selectdate']);
    Route::get('/reportsdownload', [App\Http\Controllers\TeamLeader\ReportsController::class, 'reportsdownload'])->name('reportsdownload');

    //User Routes
    include('team_leader/user_routes.php');

    //Dashboard Routes
    include('team_leader/dashboard_routes.php');

    //Campaign Management Routes
    include('team_leader/campaign_routes.php');

    //Campaign Assign Routes
    include('team_leader/campaign_assign_routes.php');

    //Team Management Routes
    include('team_leader/team_routes.php');

    //Campaign Management Routes
    include('team_leader/lead_routes.php');

    //Data Routes
    include('team_leader/data_routes.php');

    //Campaign Issue Routes
    include('team_leader/campaign_issue_routes.php');

});

Route::prefix('agent')->middleware(['web', 'check.agent'])->name('agent.')->group(function (){

    Route::get('/dashboard', [App\Http\Controllers\Agent\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [App\Http\Controllers\Agent\ReportsController::class, 'index'])->name('reports');
    Route::post('/reportshow', [App\Http\Controllers\Agent\ReportsController::class, 'selectdate']);
    //User Routes
    include('agent/user_routes.php');

    //Dashboard Routes
    include('agent/dashboard_routes.php');

    //Campaign Management Routes
    include('agent/campaign_routes.php');

    //Campaign Management Routes
    include('agent/lead_routes.php');

    //Data Routes
    include('agent/data_routes.php');

    //Campaign Issue Routes
    include('agent/campaign_issue_routes.php');

});

Route::prefix('qa-team-leader')->middleware(['web', 'check.qa_team_leader'])->name('qa_team_leader.')->group(function (){

    Route::get('/dashboard', [App\Http\Controllers\QATeamLeader\DashboardController::class, 'index'])->name('dashboard');

    //User Routes
    include('qa_team_leader/user_routes.php');

    //Campaign Management Routes
    include('qa_team_leader/campaign_routes.php');

    //Campaign Assign Routes
    include('qa_team_leader/campaign_assign_routes.php');

    //Team Management Routes
    include('qa_team_leader/team_routes.php');
    include('qa_team_leader/dashboard_routes.php');


});

    Route::prefix('quality-analyst')->middleware(['web', 'check.quality_analyst'])->name('quality_analyst.')->group(function (){
    
    Route::get('/reports', [App\Http\Controllers\QualityAnalyst\ReportsController::class, 'index'])->name('reports');

    Route::get('/dashboard', [App\Http\Controllers\QualityAnalyst\DashboardController::class, 'index'])->name('dashboard');

    //User Routes
    include('quality_analyst/user_routes.php');

    //Campaign Management Routes
    include('quality_analyst/campaign_routes.php');

    //Campaign Management Routes
    include('quality_analyst/lead_routes.php');

    //Dashboard Routes
    include('quality_analyst/dashboard_routes.php');

});

Route::prefix('email-marketing-executive')->middleware(['web', 'check.email_marketing_executive'])->name('email_marketing_executive.')->group(function (){

    Route::get('/dashboard', [App\Http\Controllers\EmailMarketingExecutive\DashboardController::class, 'index'])->name('dashboard');

    //User Routes
    include('email_marketing_executive/user_routes.php');

    //My Campaign Routes
    include('email_marketing_executive/campaign_routes.php');

    //Lead Routes
    include('email_marketing_executive/lead_routes.php');

    //Data Routes
    include('email_marketing_executive/data_routes.php');

    //Campaign Issue Routes
    include('email_marketing_executive/campaign_issue_routes.php');

    //Campaign Management Routes
    include('email_marketing_executive/campaign_management_routes.php');

    //My Promotion Campaign Routes
    include('email_marketing_executive/promotion_campaign_routes.php');

    include('email_marketing_executive/dashboard_routes.php');


});

Route::prefix('vendor-manager')->middleware(['web', 'check.vendor_manager'])->name('vendor_manager.')->group(function (){

    Route::get('/dashboard', [App\Http\Controllers\VendorManager\DashboardController::class, 'index'])->name('dashboard');

    //User Routes
    include('vendor_manager/user_routes.php');

    //Vendor Routes
    include('vendor_manager/vendor_routes.php');

    //Campaign Management Routes
    include('vendor_manager/campaign_routes.php');

    //Campaign Assign Routes
    include('vendor_manager/campaign_assign_routes.php');

    //Campaign Management Routes
    include('vendor_manager/lead_routes.php');
    include('vendor_manager/dashboard_routes.php');


    Route::prefix('ra')->name('ra.')->group(function()
    {
        //Campaign Management Routes
        include('vendor_manager/ra/campaign_routes.php');

        //Campaign Management Routes
        include('vendor_manager/ra/lead_routes.php');

        //Data Routes
        include('vendor_manager/ra/data_routes.php');

        //Campaign Issue Routes
        include('vendor_manager/ra/campaign_issue_routes.php');
    });

});


//Holiday Routes
Route::prefix('holiday')->name('holiday.')->group(function()
{
    Route::get('/list', [App\Http\Controllers\Admin\HolidayController::class, 'index'])->name('list');
    Route::any('/get-holidays', [App\Http\Controllers\Admin\HolidayController::class, 'getHolidays'])->name('get_holidays');

    Route::any('/get-holiday-list', [App\Http\Controllers\Manager\HolidayController::class, 'getHolidayList'])->name('get_holiday_list');
});

//Notification Routes

Route::any('/notification/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notification.mark_all_as_read');
Route::any('/notification/view-details/{id}', [App\Http\Controllers\NotificationController::class, 'update'])->name('notification.view_details');


Route::middleware(['web', 'auth'])->group(function (){

    Route::any('/user/update-profile', [App\Http\Controllers\UserController::class, 'uploadProfile'])->name('user.upload_profile');

    //Tutorial Links Routes
    Route::prefix('tutorial')->name('tutorial.')->group(function()
    {
        Route::get('/list', [App\Http\Controllers\TutorialController::class, 'index'])->name('list');
        Route::any('/get-tutorials', [App\Http\Controllers\TutorialController::class, 'getTutorials'])->name('get_tutorials');
    });

});

Route::get('refresh-csrf', function(){
    return csrf_token();
});





