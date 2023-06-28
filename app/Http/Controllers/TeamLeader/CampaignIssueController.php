<?php

namespace App\Http\Controllers\TeamLeader;

use App\Http\Controllers\Controller;
use App\Repository\Campaign\IssueRepository\IssueRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignIssueController extends Controller
{
    private $data;
    /**
     * @var IssueRepository
     */
    private $issueRepository;

    public function __construct(
        IssueRepository $issueRepository
    )
    {
        $this->data = array();
        $this->issueRepository = $issueRepository;
    }

    public function store(Request $request)
    {
        $attributes = $request->all();
        $attributes['campaign_id'] = base64_decode($attributes['campaign_id']);
        $response = $this->issueRepository->store($attributes);
        if($response['status'] == TRUE) {
            return back()->withInput()->with('success', ['title' => 'Successful', 'message' => $response['message']]);
            //return redirect()->route('agent.lead.list', $attributes['ca_agent_id'])->with('success', ['title' => 'Successful', 'message' => $response['message']]);
        } else {
            return back()->withInput()->with('error', ['title' => 'Error while processing request', 'message' => $response['message']]);
        }
    }

    public function update($id, Request $request)
    {
        $attributes = $request->all();
        $attributes['closed_by'] = Auth::id();
        $attributes['status'] = 1;
        $response = $this->issueRepository->update(base64_decode($id), $attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => 'Issue closed successfully'));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }
}
