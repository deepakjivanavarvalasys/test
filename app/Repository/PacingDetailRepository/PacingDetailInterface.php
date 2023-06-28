<?php

namespace App\Repository\PacingDetailRepository;

interface PacingDetailInterface
{
    public function get($campaign_id = null,$filters = array());
    public function find($id);
    public function store($campaign_id, $attributes);
    public function update($campaign_id, $attributes);
    public function destroy($id);
}
