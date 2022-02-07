<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const GUARD = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {

        return $this->hasOne(Profile::class);
    }


    public function company()
    {

        return $this->belongsTo(Company::class);
    }


    public function objectives()
    {

        return $this->hasMany(Objective::class);
    }

    public function keyResults()
    {

        return $this->hasMany(KeyResult::class);
    }

    public function teams()
    {

        return $this->hasMany(Team::class);
    }

    public function members()
    {

        return $this->hasMany(Team::class);
    }

    public function invitations()
    {

        return $this->hasMany(Invitation::class);
    }
}
