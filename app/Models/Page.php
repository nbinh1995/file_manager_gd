<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages';
    protected $guarded = [];

    public function rawUser(){
        return $this->hasOne(User::class,'raw_id','id');
    }

    public function cleanUser(){
        return $this->hasOne(User::class,'clean_id','id');
    }

    public function typeUser(){
        return $this->hasOne(User::class,'type_id','id');
    }

    public function sfxUser(){
        return $this->hasOne(User::class,'sfx_id','id');
    }

    public function checkUser(){
        return $this->hasOne(User::class,'check_id','id');
    }
}
