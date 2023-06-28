<?php

namespace App\Repository\Notification\QATL;

interface QATLNotificationInterface
{
    public function get($filters = array());
    public function find($id);
    public function store($attributes);
    public function destroy($id);
}
