<?php

namespace App\Repository\History;

interface HistoryInterface
{
    public function get($filters = array());
    public function store($attributes);
}
