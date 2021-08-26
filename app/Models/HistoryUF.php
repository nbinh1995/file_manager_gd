<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class HistoryUF extends Model
{
    protected $table = 'histories_upload_file';
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
