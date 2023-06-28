<?php

namespace App\Repository\AgentWorkType;

interface AgentWorkTypeInterface
{
    public function get($filters = array());
    public function find($id);
    public function store($attributes);
    public function update($id, $attributes);
    public function destory($id);
}
