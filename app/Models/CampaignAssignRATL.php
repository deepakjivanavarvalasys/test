<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignAssignRATL extends Model
{
    use SoftDeletes;
    protected $guarded = array();
    public $timestamps = true;

    protected $appends = ['agent_lead_total_count', 'count_agent_leads_send_to_qc'];

    public function campaign()
    {
        return $this->hasOne(Campaign::class, 'id', 'campaign_id');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function userAssignedBy()
    {
        return $this->hasOne(User::class, 'id', 'assigned_by');
    }

    public function agents()
    {
        return $this->hasMany(CampaignAssignAgent::class, 'campaign_assign_ratl_id', 'id');
    }

    public function getAgentLeadTotalCountAttribute()
    {
        $total_count = 0;
        if(isset($this->agents) && !empty($this->agents)) {
            foreach ($this->agents as $ca_agent) {
                $total_count = $total_count + $ca_agent->agent_lead_count;
            }
        }
        return $total_count;
    }

    public function getCountAgentLeadsSendToQcAttribute()
    {
        $total_count = 0;
        if(isset($this->agents) && !empty($this->agents)) {
            foreach ($this->agents as $ca_agent) {
                $total_count = $total_count + $ca_agent->agent_leads_send_to_qc->count();
            }
        }
        return $total_count;
    }

}
