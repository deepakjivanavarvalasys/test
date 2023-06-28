<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignAssignAgent extends Model
{
    use SoftDeletes;
    protected $guarded = array();
    public $timestamps = true;

    protected $appends = ['agent_lead_count', 'count_agent_leads_send_to_qc'];

    public function campaign()
    {
        return $this->hasOne(Campaign::class, 'id', 'campaign_id');
    }

    public function agent_work_type()
    {
        return $this->hasOne(AgentWorkType::class, 'id', 'agent_work_type_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function userAssignedBy()
    {
        return $this->hasOne(User::class, 'id', 'assigned_by');
    }

    public function caratl()
    {
        return $this->hasOne(CampaignAssignRATL::class, 'id', 'campaign_assign_ratl_id');
    }

    public function agentLeads()
    {
        return $this->hasMany(AgentLead::class, 'ca_agent_id', 'id');
    }

    public function agent_leads_send_to_qc()
    {
        return $this->hasMany(AgentLead::class, 'ca_agent_id', 'id')->whereNotNull('send_date');
    }

    public function getAgentLeadCountAttribute()
    {
        return $this->agentLeads->count();
    }

    public function getCountAgentLeadsSendToQcAttribute()
    {
        return $this->agent_leads_send_to_qc->count();
    }
}
