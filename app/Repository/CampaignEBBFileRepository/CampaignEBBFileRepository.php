<?php

namespace App\Repository\CampaignEBBFileRepository;

use App\Models\Campaign;
use App\Models\CampaignAssignEME;
use App\Models\CampaignEBBFile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CampaignEBBFileRepository implements CampaignEBBFileInterface
{
    /**
     * @var CampaignEBBFile
     */
    private $campaignEBBFile;

    public function __construct(
        CampaignEBBFile $campaignEBBFile
    )
    {

        $this->campaignEBBFile = $campaignEBBFile;
    }

    public function get($filters = array())
    {
        $query = CampaignEBBFile::query();

        if(isset($filters['campaign_ids']) && !empty($filters['campaign_ids'])) {
            $query->whereIn('campaign_id', $filters['campaign_ids']);
        }

        return $query->get();
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    public function store($attributes): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $file = $attributes['ebb_file'];
            $resultCampaign = Campaign::findOrFail($attributes['campaign_id']);
            $path = 'public/campaigns/'.$resultCampaign->campaign_id.'/quality/npf';
            $extension = $file->getClientOriginalExtension();
            $filename  = $file->getClientOriginalName();
            if($file->storeAs($path, $filename)) {
                $campaign_ebb_file = new CampaignEBBFile();
                $campaign_ebb_file->ca_eme_id = $attributes['ca_eme_id'];
                $campaign_ebb_file->campaign_id = $attributes['campaign_id'];
                $campaign_ebb_file->file_name = $filename;
                $campaign_ebb_file->extension = $extension;
                $campaign_ebb_file->save();
                if($campaign_ebb_file->id) {
                    //Add Campaign History
                    $resultCampaign = Campaign::findOrFail($campaign_ebb_file->campaign_id);
                    $resultEME = CampaignAssignEME::findOrFail($campaign_ebb_file->ca_eme_id);
                    $resultUser = User::findOrFail($resultEME->user_id);
                    add_campaign_history($resultCampaign->id, $resultCampaign->parent_id, 'Email Bounce Back file uploaded by -'.$resultUser->full_name);
                    add_history('EBB File Uploaded', 'Email Bounce Back file uploaded by -'.$resultUser->full_name);

                    DB::commit();
                    $response = array('status' => TRUE, 'message' => 'EBB File uploaded successfully');
                } else {
                    throw new \Exception('Something went wrong, please try again.', 1);
                }
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
        // TODO: Implement update() method.
    }

    public function destroy($id)
    {
        // TODO: Implement destroy() method.
    }
}
