<?php
/*************************************************
 * Titan-2 Mini Framework
 * Controller
 *
 * Author   : Turan Karatuğ
 * Web      : http://www.titanphp.com
 * Docs     : http://kilavuz.titanphp.com
 * Github   : http://github.com/tkaratug/titan2
 * License  : MIT
 *
 *************************************************/
namespace System\Kernel;

class Controller
{
    public function __construct()
    {
        // Run default middlewares
		$this->middleware(config('services.middlewares.default'), true);
    }

    /**
	 * Run middlewares at first
	 *
	 * @param array $middleware
	 * @param bool $default
	 * @return void
	 */
	protected function middleware(array $middlewares, bool $default = false)
	{
		if ($default === false) {
			$list = config('services.middlewares.manual');

			foreach ($middlewares as $middleware) {
				$middleware = ucfirst($middleware);
				if (array_key_exists($middleware, $list)) {
					if (class_exists($list[$middleware])) {
						call_user_func_array([new $list[$middleware], 'handle'], []);
					}
				}
			}
		} else {
			foreach ($middlewares as $key => $val) {
				if (class_exists($val)) {
					call_user_func_array([new $val, 'handle'], []);
				}
			}
		}
	}
}
