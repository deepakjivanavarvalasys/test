<?php

namespace App\Repository\AgentDataRepository;

interface AgentDataInterface
{
    public function get($filters = array());
    public function find($id);
    public function store($attributes);
    public function assignData($attributes);
    public function update($id,$attributes);
    public function destroy($id);
}
