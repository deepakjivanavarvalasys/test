<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyReportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CronTriggerController extends Controller
{
    private $data;
    private $yesterday_shirt_from;
    private $yesterday_shirt_to;

    public function __construct()
    {
        $this->data = array();

        if(date('H') >= 12 && date('H') <= 23) {
            $this->yesterday_shirt_from = date('Y-m-d 12:00:00', strtotime('-1 day'));
            $this->yesterday_shirt_to = date('Y-m-d 11:59:59');
        } else {
            $this->yesterday_shirt_from = date('Y-m-d 12:00:00', strtotime('-2 day'));
            $this->yesterday_shirt_to = date('Y-m-d 11:59:59', strtotime('-1 day'));
        }
    }

    public function index()
    {
        $countDailyReportLogCron = DailyReportLog::where('cron_status', 1)->whereBetween('sign_in', [$this->yesterday_shirt_from, $this->yesterday_shirt_to])->count();
        $this->data['dailyreportlog_cron_status'] = !($countDailyReportLogCron > 0);

        return view('admin.cron_trigger.list', $this->data);
    }

    public function DailyReportLog()
    {
        Artisan::call('dailyreportlog:cron');
        $response = Artisan::output();
        if(trim($response) == 'true') {
            return back()->with('success', ['title' => 'Successful', 'message' => 'Daily Report Logs: Cron completed successfully.']);
        } else {
            return back()->with('error', ['title' => 'Error while processing request', 'message' => trim($response)]);
        }
    }
}
