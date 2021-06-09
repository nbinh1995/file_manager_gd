<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages';
    protected $guarded = [];

    public function rawUser(){
        return $this->hasOne(User::class,'id','raw_id');
    }

    public function cleanUser(){
        return $this->hasOne(User::class,'id','clean_id');
    }

    public function typeUser(){
        return $this->hasOne(User::class,'id','type_id');
    }

    public function sfxUser(){
        return $this->hasOne(User::class,'id','sfx_id');
    }

    public function checkUser(){
        return $this->hasOne(User::class,'id','check_id');
    }

    public function volume(){
        return $this->belongsTo(Volume::class,'volume_id','id');
    }
}
