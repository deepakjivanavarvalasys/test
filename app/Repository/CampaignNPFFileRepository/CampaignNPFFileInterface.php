<?php

namespace App\Repository\CampaignNPFFileRepository;

interface CampaignNPFFileInterface
{
    public function get($filters = array());
    public function find($id);
    public function store($attributes);
    public function update($id, $attributes);
    public function destroy($id);
}
