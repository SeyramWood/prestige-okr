<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamObjective extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function objective()
    {
        return $this->belongsTo(Objective::class);
    }
}
