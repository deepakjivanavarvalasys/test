<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignCountry extends Model
{
    protected $guarded = array();
    public $timestamps = true;

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
