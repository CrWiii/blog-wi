<?php

namespace App;

class Post extends Model{
	
	public function comments(){
		return $this->hasMany(Comment::class);
	}
	public function addComments($body){
		$this->comments()->create(compact('body'));
		//Comment::create(['body'=> $body,'post_id'=>$this->id]);
	}

}
