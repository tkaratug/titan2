<?php
namespace App\Middlewares;

class Auth
{

	public static function handle()
	{
		echo 'Erişim Engellendi!';
		exit();
	}

}