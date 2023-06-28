<?php

namespace App\Repository\CampaignAssignRepository\EMERepository;

interface EMEInterface
{
    public function get($filters = array());
    public function find($id);
    public function store($attributes);
    public function update($id, $attributes);
    public function destroy($id);
}
