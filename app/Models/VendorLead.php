<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorLead extends Model
{
    use SoftDeletes;
    protected $guarded = array();
    public $timestamps = true;

    public function campaign()
    {
        return $this->hasOne(Campaign::class, 'id', 'campaign_id');
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'id', 'vendor_id');
    }

    public function ca_vendor()
    {
        return $this->hasOne(CampaignAssignVendor::class, 'id', 'ca_vendor_id');
    }
}
