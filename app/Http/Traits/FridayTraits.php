<?php

namespace App\Http\Traits;
use App\Employee;

trait FridayTraits {

	public function checkFriday()
	{

	}

    public function extract() 
    {
        $employee = Employee::all();
        return view('home')->with(compact('employee'));
    }

}