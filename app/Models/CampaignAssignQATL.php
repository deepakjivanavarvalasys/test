<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignAssignQATL extends Model
{
    use SoftDeletes;
    protected $guarded = array();
    public $timestamps = true;

    protected $appends = ['agent_lead_total_count'];

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

    public function caratls()
    {
        return $this->hasMany(CampaignAssignRATL::class, 'campaign_id', 'campaign_id');
    }

    public function quality_analyst()
    {
        return $this->hasOne(CampaignAssignQualityAnalyst::class, 'campaign_assign_qatl_id', 'id')->where('status', 1);
    }

    public function quality_analysts()
    {
        return $this->hasMany(CampaignAssignQualityAnalyst::class, 'campaign_assign_qatl_id', 'id');
    }

    public function getAgentLeadTotalCountAttribute()
    {
        $total_count = 0;
        if(isset($this->caratls) && !empty($this->caratls)) {
            foreach ($this->caratls as $caratl) {
                if(isset($caratl->agents) && !empty($caratl->agents)) {
                    foreach ($caratl->agents as $ca_agent) {
                        $total_count = $total_count + $ca_agent->agent_lead_count;
                    }
                }
            }
        }
        return $total_count;
    }
}
