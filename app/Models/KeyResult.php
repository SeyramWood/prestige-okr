<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeyResult extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function objective()
    {
        return $this->belongsTo(Objective::class);
    }
    public function krMetric()
    {
        return $this->hasOne(KeyResultMetric::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
