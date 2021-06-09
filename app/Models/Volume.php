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
}
