<?php

namespace App\Console\Commands;

use App\Models\AgentLead;
use App\Models\DailyReportLog;
use App\Models\Role;
use App\Models\TransactionTime;
use Illuminate\Console\Command;

class DailyReportLogCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dailyreportlog:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is daily report logs cron job which make the daily report of agents';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = 'Something went wrong.!!!';
        try {

            $role_ids = Role::whereIn('slug', array('research_analyst', 'email_marketing_executive', 'vendor_management'))->where('status', 1)->get();
            /*
             * get user list whose yesterday's daily report is not updated
             */
            $query = DailyReportLog::query();
            //$query->where('productivity', 0)->where('quality', 0);
            $query->where('cron_status', 0);
            $query->whereHas('user', function ($sub_query) use($role_ids) {
                $sub_query->whereIn('role_id', $role_ids->pluck('id')->toArray());
            });

            $time = date('H');

            if($time >= 12 && $time <= 23) {
                $from = date('Y-m-d 12:00:00', strtotime('-1 day'));
                $to = date('Y-m-d 11:59:59');
            } else {
                $from = date('Y-m-d 12:00:00', strtotime('-2 day'));
                $to = date('Y-m-d 11:59:59', strtotime('-1 day'));
            }

            $query->whereBetween('sign_in', [$from, $to]);
            $resultUsers = $query->get();

            if(!empty($resultUsers) && $resultUsers->count()) {
                foreach($resultUsers as $key => $user) {

                    $resultAgentLeads = AgentLead::where('agent_id', $user->user_id)->whereBetween('created_at', [$from, $to])->get();
                    $total_leads = AgentLead::where('agent_id', $user->user_id)->whereBetween('created_at', [$from, $to])->count();
                    $total_qualified_leads = AgentLead::where('agent_id', $user->user_id)->whereBetween('created_at', [$from, $to])->where('status', '1')->count();

                    //Initialize calculation values
                    $total_production_time = 0; //in seconds
                    $ca_agent_ids_abm = null;
                    $total_accounts_utilized = 0;

                    foreach ($resultAgentLeads as $agentLead) {
                        switch ($agentLead->ca_agent->agent_work_type->slug) {
                            case 'cd':
                            case 'cdqa':
                            case 'lead_nurture':
                            case 'address_fetch':
                                $total_production_time += $agentLead->transaction_time;
                                break;
                            case 'abm':
                                $total_production_time += $agentLead->transaction_time;
                                $ca_agent_ids_abm[$agentLead->ca_agent->id] = $agentLead->ca_agent->accounts_utilized;
                                break;
                        }
                    } //end:agentLead's foreach

                    //Calculate ABM extra worked time
                    if(!empty($ca_agent_ids_abm) && count($ca_agent_ids_abm)) {
                        foreach ($ca_agent_ids_abm as $ca_agent_id => $accounts_utilized) {
                            $total_accounts_utilized += $accounts_utilized;
                        }

                        $resultTransactionTime = TransactionTime::where('status', 1)->orderBy('created_at', 'desc')->first();
                        $abm_extra_time = round($total_accounts_utilized * 0.1) * $resultTransactionTime->abm;

                        $total_production_time = $total_production_time + $abm_extra_time;
                    }

                    $production_time = ($total_production_time / 60); //convert production time: seconds to minutes

                    $productivity = ($production_time / 465) * 100;

                    //Round up to nearest number
                    $productivity = (int) number_format((float)($productivity + 0.4), 0, '.', '');

                    $quality = $total_leads > 0 ? (($total_qualified_leads / $total_leads) * 100) : 0;
                    $quality = (int) number_format((float)($quality + 0.4), 0, '.', '');

                    //Save to database
                    $user->lead_count = $total_leads;
                    $user->productivity = $productivity;
                    $user->quality = $quality;
                    $user->cron_status = 1;

                    if($user->save()) {
                        //$response = 'Cron completed successfully - Daily Report Logs.!';
                        $response = 'true';
                    } else {
                        throw new \Exception('server_error');
                    }

                } //end:user's foreach
            } else {
                throw new \Exception('Daily report logs already updated for yesterday OR new users not found to update.');
            }

        } catch (\Exception $exception) {
            $response = $exception->getMessage();
        }

        $this->info($response);

        //return Command::SUCCESS;
    }
}
