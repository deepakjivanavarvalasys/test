<?php

namespace App\Repository\Notification\QA;

interface QANotificationInterface
{
    public function get($filters = array());
    public function find($id);
    public function store($attributes);
    public function destroy($id);
}
