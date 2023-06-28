<?php

namespace App\Repository\CampaignNPFFileRepository;

use App\Models\CampaignNPFFile;

class CampaignNPFFileRepository implements CampaignNPFFileInterface
{
    /**
     * @var CampaignNPFFile
     */
    private $campaignNPFFile;

    public function __construct(
        CampaignNPFFile $campaignNPFFile
    )
    {

        $this->campaignNPFFile = $campaignNPFFile;
    }

    public function get($filters = array())
    {
        $query = CampaignNPFFile::query();

        if(isset($filters['campaign_ids']) && !empty($filters['campaign_ids'])) {
            $query->whereIn('campaign_id', $filters['campaign_ids']);
        }

        if(isset($filters['ca_eme_ids']) && $filters['ca_eme_ids']) {
            $query->whereIn('ca_eme_id', $filters['ca_eme_ids']);
        }
        return $query->get();
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    public function store($attributes)
    {
        // TODO: Implement store() method.
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
