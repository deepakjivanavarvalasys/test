<?php

namespace App\Repository\Campaign\IssueRepository;

use App\Models\Campaign;
use App\Models\CampaignAssignAgent;
use App\Models\CampaignAssignRATL;
use App\Models\CampaignIssue;
use App\Models\ManagerNotification;
use App\Models\RANotification;
use App\Models\RATLNotification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IssueRepository implements IssueInterface
{
    /**
     * @var CampaignIssue
     */
    private $campaignIssue;

    public function __construct(
        CampaignIssue $campaignIssue
    )
    {
        $this->campaignIssue = $campaignIssue;
    }

    public function get($filters = array())
    {
        $query = CampaignIssue::query();

        if(isset($filters['campaign_ids']) && !empty($filters['campaign_ids'])) {
            $query->whereIn('campaign_id', $filters['campaign_ids']);
        }

        $query->with(['campaign', 'user', 'closed_by_user']);

        return $query->get();
    }



    public function find($id)
    {
        $query = CampaignIssue::query();

        return $query->find($id);
    }

    public function store($attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();

            $campaign_issue = new CampaignIssue();
            $campaign_issue->campaign_id = $attributes['campaign_id'];
            $campaign_issue->user_id = Auth::id();
            $campaign_issue->priority = strtolower($attributes['priority']);
            $campaign_issue->title = $attributes['title'];
            $campaign_issue->description = $attributes['description'];
            $campaign_issue->save();
            if($campaign_issue->id) {
                //Send Notification
                $dataNotification = array();
                $resultManagers = User::whereHas('role', function ($role){ $role->where('slug', 'manager')->where('status', 1); })->where('status', 1)->where('id','!=', Auth::id())->get();
                if(!empty($resultManagers) && $resultManagers->count()) {
                    foreach ($resultManagers as $manager) {
                        $dataNotification[] = array(
                            'sender_id' => Auth::id(),
                            'recipient_id' => $manager->id,
                            'message' => 'Campaign issue raised by - '.Auth::user()->full_name,
                            'url' => route('manager.campaign_assign.show', base64_encode($campaign_issue->campaign_id))
                        );
                    }
                    $responseNotification = ManagerNotification::insert($dataNotification);
                }
                if(Auth::user()->role->slug == 'research_analyst') {
                    $dataNotification = array();
                    $dataNotification[] = array(
                        'sender_id' => Auth::id(),
                        'recipient_id' => Auth::user()->reporting_user_id,
                        'message' => 'Campaign issue raised by - '.Auth::user()->full_name,
                        'url' => route('team_leader.campaign_assign.show', base64_encode($campaign_issue->campaign_id))
                    );
                    $responseNotification = RATLNotification::insert($dataNotification);
                }
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Issues raised successfully');
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
            $campaign_issue = CampaignIssue::findOrFail($id);

            if(isset($attributes['campaign_id']) && !empty($attributes['campaign_id'])) {
                $campaign_issue->campaign_id = $attributes['campaign_id'];
            }

            if(isset($attributes['user_id']) && !empty($attributes['user_id'])) {
                $campaign_issue->user_id = $attributes['user_id'];
            }

            if(isset($attributes['priority']) && !empty($attributes['priority'])) {
                $campaign_issue->priority = strtolower($attributes['priority']);
            }

            if(isset($attributes['title']) && !empty($attributes['title'])) {
                $campaign_issue->title = $attributes['title'];
            }

            if(isset($attributes['description']) && !empty($attributes['description'])) {
                $campaign_issue->description = $attributes['description'];
            }

            if(isset($attributes['response']) && !empty($attributes['response'])) {
                $campaign_issue->response = $attributes['response'];
            }

            if(isset($attributes['closed_by']) && !empty($attributes['closed_by'])) {
                $campaign_issue->closed_by = $attributes['closed_by'];
            }

            if(array_key_exists('status', $attributes)) {
                $campaign_issue->status = $attributes['status'];
            }

            if($campaign_issue->save()) {

                //Send Notification to manager
                $resultManagers = User::whereHas('role', function ($role){ $role->where('slug', 'manager')->where('status', 1); })->where('status', 1)->where('id','!=', Auth::id())->get();
                $resultUser = User::findOrFail($campaign_issue->user_id);
                //Send Notification to issue raise by
                $dataNotification = array();

                switch ($resultUser->role->slug) {
                    case 'research_analyst' :
                        $resultCAAgent = CampaignAssignAgent::where('campaign_id', $campaign_issue->campaign_id)->where('user_id', $campaign_issue->user_id)->first();
                        $dataNotification[] = array(
                            'sender_id' => Auth::id(),
                            'recipient_id' => $campaign_issue->user_id,
                            'message' => 'Campaign issue updated by - '.Auth::user()->full_name,
                            'url' => route('agent.campaign.show', base64_encode($resultCAAgent->id))
                        );
                        $responseNotification = RANotification::insert($dataNotification);
                        if($resultUser->reporting_user_id != Auth::id()) {
                            $dataNotification = array();
                            $dataNotification[] = array(
                                'sender_id' => Auth::id(),
                                'recipient_id' => $resultUser->reporting_user_id,
                                'message' => 'Campaign issue updated by - '.Auth::user()->full_name,
                                'url' => route('team_leader.campaign_assign.show', base64_encode($resultCAAgent->campaign_assign_ratl_id))
                            );
                            $responseNotification = RATLNotification::insert($dataNotification);
                        }
                        break;
                    case 'team_leader' :
                        $resultCARATL = CampaignAssignRATL::where('campaign_id', $campaign_issue->campaign_id)->where('user_id', $campaign_issue->user_id)->first();
                        $dataNotification = array();
                        $dataNotification[] = array(
                            'sender_id' => Auth::id(),
                            'recipient_id' => $resultUser->reporting_user_id,
                            'message' => 'Campaign issue updated by - '.Auth::user()->full_name,
                            'url' => route('team_leader.campaign_assign.show', base64_encode($resultCARATL->id))
                        );
                        $responseNotification = RATLNotification::insert($dataNotification);
                        break;
                }

                if(!empty($resultManagers) && $resultManagers->count()) {
                    $dataNotification = array();

                    foreach ($resultManagers as $manager) {
                        $dataNotification[] = array(
                            'sender_id' => Auth::id(),
                            'recipient_id' => $manager->id,
                            'message' => 'Campaign issue updated by - '.Auth::user()->full_name,
                            'url' => route('manager.campaign_assign.show', base64_encode($campaign_issue->campaign_id))
                        );
                    }
                    $responseNotification = ManagerNotification::insert($dataNotification);
                }

                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Issues updated successfully');
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
