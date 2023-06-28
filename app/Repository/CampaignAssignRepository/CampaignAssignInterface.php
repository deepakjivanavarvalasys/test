<?php

namespace App\Repository\CampaignAssignRepository;

interface CampaignAssignInterface
{
    public function getAssignedCampaigns($filters = array());
    public function getCampaignToAssign($filters = array());
    public function store($attributes);
}
