<?php

namespace App\Repository\DataRepository;

interface DataInterface
{
    public function get($filters = array());
    public function find($id);
    public function store($attributes);
    public function update($id, $attributes);
    public function destroy($id);

    public function import($file);
}
