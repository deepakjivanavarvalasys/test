<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignHistory extends Model
{
    protected $guarded = array();
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
