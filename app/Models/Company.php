<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function admin()
    {

        return $this->hasOne(Admin::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
