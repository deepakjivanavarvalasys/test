<?php

namespace App\Repository\Notification\RA;

interface RANotificationInterface
{
    public function get($filters = array());
    public function find($id);
    public function store($attributes);
    public function destroy($id);
}
