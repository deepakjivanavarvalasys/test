<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Repository\Campaign\IssueRepository\IssueRepository;
use Illuminate\Http\Request;

class CampaignIssueController extends Controller
{
    private $data;
    /**
     * @var IssueRepository
     */
    private $issueRepository;

    public function __construct(IssueRepository $issueRepository)
    {

        $this->issueRepository = $issueRepository;
    }


    public function index()
    {
        return 'Work in progress';
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
}
