<?php
namespace App\Listeners;

use Log;

class SampleListener
{

	public function handle()
	{
		Log::info('listener tetiklendi - {SampleListener}');
	}

}