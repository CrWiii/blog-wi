<?php

namespace App\Http\Controllers;
use App\Tasks;
use Illuminate\Http\Request;

class TasksController extends Controller{
    public function index(){
		$tasks = Tasks::All();//incomplete()->where('id','>=',2)->get();
    	return view('tasks.index',compact('tasks'));
    }

    public function show(Tasks $task){
		return view('tasks.show', compact('task'));
    }
}
