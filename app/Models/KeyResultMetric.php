<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeyResultMetric extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function keyResult()
    {
        return $this->belongsTo(KeyResult::class);
    }
}
