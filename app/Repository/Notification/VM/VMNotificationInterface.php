<?php

namespace App\Repository\Notification\VM;

interface VMNotificationInterface
{
    public function get($filters = array());
    public function find($id);
    public function store($attributes);
    public function destroy($id);
}
