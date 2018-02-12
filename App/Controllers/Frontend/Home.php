<?php
namespace App\Controllers\Frontend;

use System\Kernel\Controller;
use View;

class Home extends Controller
{

	public function index()
	{
		View::render('home');
	}

}
