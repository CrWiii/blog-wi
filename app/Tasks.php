<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tasks extends Model{
    /*public static function incomplete(){
    	return static::where('complete', 0)->get();
    }*/
    public function scopeIncomplete($query){
    	return $query->where('complete',0);
    }

}
