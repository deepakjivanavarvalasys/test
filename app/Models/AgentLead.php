<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentLead extends Model
{
    use SoftDeletes;
    protected $guarded = array();
    public $timestamps = true;

    public function campaign()
    {
        return $this->hasOne(Campaign::class, 'id', 'campaign_id');
    }

    public function agent()
    {
        return $this->hasOne(User::class, 'id', 'agent_id');
    }

    public function ca_agent()
    {
        return $this->hasOne(CampaignAssignAgent::class, 'id', 'ca_agent_id');
    }

}
