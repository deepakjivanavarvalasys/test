<?php

namespace App\Repository\CampaignFilterRepository;

interface CampaignFilterInterface
{
    public function get($filter = array());
    public function find($id);
    public function store($attributes);
    public function update($id, $attributes);
    public function destroy($id);
}
