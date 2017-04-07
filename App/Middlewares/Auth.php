<?php
namespace App\Middlewares;

class Auth
{

	public static function next()
	{
		echo 'Erişim Engellendi!';
		exit();
	}

}