<?php

namespace App\Repository\CampaignFile;

use App\Models\CampaignFile;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class CampaignFileRepository implements CampaignFileInterface
{
    private $campaignFile;

    public function __construct(
        CampaignFile $campaignFile
    )
    {
        $this->campaignFile = $campaignFile;
    }

    public function get($filters = array())
    {
        // TODO: Implement get() method.
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    public function store($attributes)
    {

        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();

            $campaignFile = new CampaignFile();
            $campaignFile->campaign_id = $attributes['campaign_id'];
            $campaignFile->file_type = $attributes['file_type'];
            $campaignFile->file_name = $attributes['file_name'];
            $campaignFile->extension = $attributes['extension'];

            $campaignFile->save();

            if($campaignFile->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Campaign file added successfully');
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
