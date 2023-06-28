<?php

namespace App\Repository\Notification\EME;

interface EMENotificationInterface
{
    public function get($filters = array());
    public function find($id);
    public function store($attributes);
    public function destroy($id);
}
