<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PacingDetail extends Model
{
    use SoftDeletes;
    protected $guarded = array();
    public $timestamps = true;

    protected $appends = ['is_holiday'];

    public function holiday()
    {
        return $this->belongsTo(Holiday::class, 'date', 'date');
    }

    public function getIsHolidayAttribute()
    {
        return isset($this->holiday) ? 1 : 0;
    }

    public function campaign()
    {
        return $this->hasOne(Campaign::class, 'id', 'campaign_id');
    }


}
