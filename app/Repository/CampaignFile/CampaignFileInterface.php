<?php

namespace App\Repository\CampaignFile;

interface CampaignFileInterface
{
    public function get($filters = array());
    public function find($id);
    public function store($attributes);
    public function update($id, $attributes);
    public function destroy($id);
}
