<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignIssue extends Model
{
    use SoftDeletes;
    protected $guarded = array();
    public $timestamps = true;

    public function campaign()
    {
        return $this->hasOne(Campaign::class, 'id', 'campaign_id');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function closed_by_user()
    {
        return $this->hasOne(User::class, 'id', 'closed_by');
    }
}
