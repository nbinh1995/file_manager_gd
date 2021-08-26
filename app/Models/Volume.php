<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Volume extends Model
{
    protected $table = 'volumes';
    protected $guarded = [];

    public function book(){
        return $this->belongsTo(Book::class,'book_id','id');
    }

    public function pages(){
        return $this->hasMany(Page::class,'volume_id','id');
    }

    public function pages_sfx_done(){
        return $this->hasMany(Page::class,'volume_id','id')->where('sfx', 'done');
    }
    public function pages_check_done(){
        return $this->hasMany(Page::class,'volume_id','id')->where('check', 'done');
    }
}
