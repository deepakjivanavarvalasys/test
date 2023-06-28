<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\CampaignIssue;
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

    public function index()
    {
        return view('manager.campaign_issue.list', $this->data);
    }

    public function edit($id, Request $request)
    {
        $resultCampaignIssues = $this->issueRepository->find(base64_decode($id));

        if($request->ajax()){
            if(!empty($resultCampaignIssues->id)) {
                return response()->json(array('status' => true, 'message' => 'Data fetched successfully', 'data' => $resultCampaignIssues));
            } else {
                return response()->json(array('status' => false, 'message' => 'Data not found'));
            }
        } else {
            if(!empty($resultCampaignIssues->id)) {
                return view('manager.campaign_issue.edit', $this->data);
            } else {
                return back()->withInput()->with('error', ['title' => 'Error while processing request', 'message' => 'Data not found']);
            }
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

    public function getIssues(Request $request): \Illuminate\Http\JsonResponse
    {
        $filters = array_filter(json_decode($request->get('filters'), true));
        $search_data = $request->get('search');
        $filterissue = $request->filterissue;
        // echo $filterissue;
        $searchValue = $search_data['value'];

        $draw = $request->get('draw');
        $limit = $request->get("length"); // Rows display per page
        $offset = $request->get("start");

        $query = CampaignIssue::query();
        $query->with('campaign');
        $query->with('user');
        $query->with('closed_by_user');
        if($filterissue=='open')
        {
            $query->where('status','=','0');
 
        }
        
        if($filterissue=='close')
        {
            $query->where('status','=','1');
         }
        $totalRecords = $query->count();

        //Search Data
        if(isset($searchValue) && $searchValue != "") {
            $query->whereHas('campaign', function ($campaign) use($searchValue) {
                $campaign->where("campaign_id", "like", "%$searchValue%");
            });
            $query->orWhere("title", "like", "%$searchValue%");
            /*$query->where("campaign_id", "like", "%$searchValue%");
            $query->orWhere("name", "like", "%$searchValue%");*/
        }
        //Filters
        if(!empty($filters)) {

        }

        //Order By
        $orderColumn = null;
        if ($request->has('order')){
            $order = $request->get('order');
            $orderColumn = $order[0]['column'];
            $orderDirection = $order[0]['dir'];
        }

        switch ($orderColumn) {
            case '0': $query->orderBy('campaign_id', $orderDirection); break;
            case '1': $query->orderBy('name', $orderDirection); break;
            case '2':
                break;
            case '3': $query->orderBy('start_date', $orderDirection); break;
            case '4': $query->orderBy('end_date', $orderDirection); break;
            case '5': $query->orderBy('allocation', $orderDirection); break;
            case '6': $query->orderBy('campaign_status_id', $orderDirection); break;
            default: $query->orderBy('created_at', 'DESC'); break;
        }

        $totalFilterRecords = $query->count();
        if($limit > 0) {
            $query->offset($offset);
            $query->limit($limit);
        }

        $result = $query->get();

        $ajaxData = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalFilterRecords,
            "aaData" => $result
        );

        return response()->json($ajaxData);
    }
    

        
}
