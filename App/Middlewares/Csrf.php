<?php
namespace App\Middlewares;

use Session;
use System\Libs\Exception\ExceptionHandler;

class Csrf
{
    public function handle()
    {
        if (Session::has('titan_token')) {
            if (input('csrf_token') == Session::get('titan_token')) {
                Session::delete('titan_token');
                return true;
            } else {
                throw new ExceptionHandler("Hata", "CSRF Token does not match");
            }
        } else {
            return true;
        }
    }
}
