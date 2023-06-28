<?php

namespace App\Repository\Campaign\History;

interface CampaignHistoryInterface
{
    public function get($filters = array());
    public function store($attributes);
}
