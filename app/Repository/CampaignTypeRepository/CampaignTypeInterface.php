<?php

namespace App\Repository\CampaignTypeRepository;

interface CampaignTypeInterface
{
    public function get($filter = array());
    public function find($id);
    public function store($attributes);
    public function update($id, $attributes);
    public function destroy($id);
}
