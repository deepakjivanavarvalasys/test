<?php

namespace App\Repository\AgentLeadRepository;

interface AgentLeadInterface
{
    public function get($filters = array());
    public function find($id, $with = array());
    public function store($attributes);
    public function update($id, $attributes);
    public function destroy($id);
}
