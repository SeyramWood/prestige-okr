<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objective extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function keyResults()
    {
        return $this->hasMany(KeyResult::class);
    }
    public function metric()
    {
        return $this->hasOne(ObjectiveMetric::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teamObjectives()
    {
        return $this->hasMany(TeamObjective::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_objectives')->withTimestamps();
    }
}
