<?php

namespace App\Repository\DailyReportLog;

use App\Models\DailyReportLog;
use Illuminate\Support\Facades\DB;

class DailyReportLogRepository implements DailyReportLogInterface
{
    /**
     * @var DailyReportLog
     */
    private $dailyReportLog;

    public function __construct(DailyReportLog $dailyReportLog)
    {

        $this->dailyReportLog = $dailyReportLog;
    }

    public function get($filters = array())
    {
        $query = DailyReportLog::query();

        return $query->get();
    }

    public function find($id)
    {
        $query = DailyReportLog::query();

        return $query->findOrFail($id);
    }

    public function store($attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $daily_report_log = new DailyReportLog();
            $daily_report_log->user_id = $attributes['user_id'];
            $daily_report_log->sign_in = $attributes['sign_in'];

            if(isset($attributes['sign_out']) && !empty($attributes['sign_out'])) {
                $daily_report_log->sign_out = $attributes['sign_out'];
            }

            if(isset($attributes['lead_count']) && !empty($attributes['lead_count'])) {
                $daily_report_log->lead_count = $attributes['lead_count'];
            }

            if(isset($attributes['productivity']) && !empty($attributes['productivity'])) {
                $daily_report_log->productivity = $attributes['productivity'];
            }

            if(isset($attributes['quality']) && !empty($attributes['quality'])) {
                $daily_report_log->quality = $attributes['quality'];
            }

            if(array_key_exists('cron_status', $attributes)) {
                $daily_report_log->quality = $attributes['cron_status'];
            }

            if(isset($attributes['remote_address']) && !empty($attributes['remote_address'])) {
                $daily_report_log->remote_address = $attributes['remote_address'];
            }

            $daily_report_log->save();

            if($daily_report_log->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Daily Log added successfully');
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }

    public function update($id, $attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $daily_report_log = DailyReportLog::find($id);

            if(isset($attributes['user_id']) && !empty($attributes['user_id'])) {
                $daily_report_log->user_id = $attributes['user_id'];
            }

            if(isset($attributes['sign_in']) && !empty($attributes['sign_in'])) {
                $daily_report_log->sign_in = $attributes['sign_in'];
            }

            if(isset($attributes['sign_out']) && !empty($attributes['sign_out'])) {
                $daily_report_log->sign_out = $attributes['sign_out'];
            }

            if(isset($attributes['lead_count']) && !empty($attributes['lead_count'])) {
                $daily_report_log->lead_count = $attributes['lead_count'];
            }

            if(isset($attributes['productivity']) && !empty($attributes['productivity'])) {
                $daily_report_log->productivity = $attributes['productivity'];
            }

            if(isset($attributes['quality']) && !empty($attributes['quality'])) {
                $daily_report_log->quality = $attributes['quality'];
            }

            if(array_key_exists('cron_status', $attributes)) {
                $daily_report_log->quality = $attributes['cron_status'];
            }

            if(isset($attributes['remote_address']) && !empty($attributes['remote_address'])) {
                $daily_report_log->remote_address = $attributes['remote_address'];
            }

            if($daily_report_log->save()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Daily Log updated successfully');
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }

    public function destroy($id)
    {
        // TODO: Implement destroy() method.
    }
}
