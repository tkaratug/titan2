<?php

namespace App\Controllers\Frontend;

use View;

class Home
{

	public function index()
	{
		View::render('home');
	}

}