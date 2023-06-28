<?php

namespace App\Http\Controllers\QATeamLeader;

use App\Http\Controllers\Controller;
use App\Models\CampaignAssignQATL;
use App\Repository\CampaignAssignRepository\QATLRepository\QATLRepository;
use App\Repository\CampaignRepository\CampaignRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    private $data;
    /**
     * @var QATLRepository
     */
    private $QATLRepository;
    /**
     * @var CampaignRepository
     */
    private $campaignRepository;

    public function __construct(
        QATLRepository $QATLRepository,
        CampaignRepository $campaignRepository
    )
    {
        $this->data = array();
        $this->QATLRepository = $QATLRepository;
        $this->campaignRepository = $campaignRepository;
    }

    public function index()
    {
        return view('qa_team_leader.campaign.list', $this->data);
    }

    public function show($qatl_id)
    {
        $this->data['resultCAQATL'] = $this->QATLRepository->find(base64_decode($qatl_id));
        $this->data['resultCampaign'] = $this->campaignRepository->find($this->data['resultCAQATL']->campaign->id);

        return view('qa_team_leader.campaign.show', $this->data);
    }

    public function getCampaigns(Request $request): \Illuminate\Http\JsonResponse
    {
        $filters = array_filter(json_decode($request->get('filters'), true));
        $search_data = $request->get('search');
        $searchValue = $search_data['value'];
        $order = $request->get('order');
        $draw = $request->get('draw');
        $limit = $request->get("length"); // Rows display per page
        $offset = $request->get("start");

        $query = CampaignAssignQATL::query();

        $query->whereUserId(Auth::id());

        $query->with('campaign.children');

        $query->with('caratls');

        $totalRecords = $query->count();

        //Search Data
        if(isset($searchValue) && $searchValue != "") {
            $query->whereHas('campaign', function ($campaign) use($searchValue) {
                $campaign->where("campaign_id", "like", "%$searchValue%");
                $campaign->orWhere("name", "like", "%$searchValue%");
                $campaign->orWhere("allocation", "like", "%$searchValue%");
                $campaign->orWhere("deliver_count", "like", "%$searchValue%");
            });
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
            default: $query->orderBy('created_at', 'DESC'); break;
        }

        $totalFilterRecords = $query->count();
        if($limit > 0) {
            $query->offset($offset);
            $query->limit($limit);
        }

        $result = $query->get();
        //dd($result->toArray());

        $ajaxData = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalFilterRecords,
            "aaData" => $result
        );

        return response()->json($ajaxData);
    }

}
