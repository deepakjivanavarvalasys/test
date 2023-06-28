<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionTime extends Model
{
    use SoftDeletes;
    protected $guarded = array();
    public $timestamps = true;
}
