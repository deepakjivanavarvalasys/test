<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignSpecification extends Model
{
    protected $guarded = array();
    public $timestamps = true;

    public function campaign(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Campaign::class, 'id', 'campaign_id');
    }
}
