<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages';
    protected $guarded = [];

    public function rawUser(){
        return $this->hasOne(User::class,'id','raw_id')->withTrashed();
    }

    public function cleanUser(){
        return $this->hasOne(User::class,'id','clean_id')->withTrashed();
    }

    public function typeUser(){
        return $this->hasOne(User::class,'id','type_id')->withTrashed();
    }

    public function sfxUser(){
        return $this->hasOne(User::class,'id','sfx_id')->withTrashed();
    }

    public function checkUser(){
        return $this->hasOne(User::class,'id','check_id')->withTrashed();
    }

    public function volume(){
        return $this->belongsTo(Volume::class,'volume_id','id');
    }

    static function updatePendingPages($array_id,$type){
        
        $pages = self::whereIn('id',$array_id);

        return $pages->update(
            [
                $type=>'pending'
            ]
            );
    }
}
