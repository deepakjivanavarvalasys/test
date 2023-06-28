<?php

namespace App\Repository\Campaign\DeliveryDetailRepository;

interface DeliveryDetailInterface
{
    public function get($filters = array());
    public function find($id);
    public function store($attributes);
    public function update($id = 0, $attributes);
    public function destroy($id);
}
