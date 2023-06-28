<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManagerNotification extends Model
{
    use SoftDeletes;
    protected $guarded = array();
    public $timestamps = true;

    public function sender()
    {
        return $this->hasOne(User::class, 'id', 'sender_id');
    }

    public function recipient()
    {
        return $this->hasOne(User::class, 'id', 'recipient_id');
    }
}
