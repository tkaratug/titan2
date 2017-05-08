<?php
/*************************************************
 * Titan-2 Mini Framework
 * Routing Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
namespace System\Libs\Router;

use System\Libs\Exception\ExceptionHandler;

class Router
{

	// The route patterns and their handling functions
	private static $afterRoutes 	= [];

	// The before middleware route patterns and their handling functions
	private static $beforeRoutes 	= [];

	// The function to be executed when no route has been matched
	protected static $notFoundCallback;

	// Current base route, used for (sub)route mounting
	private static $baseRoute 		= '';

	// The Request Method that needs to be handled
	private static $requestedMethod = '';

	// The Server Base Path for Router Execution
	private static $serverBasePath;

	/**
	 * Store a before middleware route and a handling function to be executed when accessed using one of the specified methods
	 *
	 * @param string $methods
	 * @param string $pattern
	 * @param object|callable $fn
	 */
	public static function before($methods, $pattern, $fn)
	{
		$pattern = self::$baseRoute . '/' . trim($pattern, '/');
		$pattern = self::$baseRoute ? rtrim($pattern, '/') : $pattern;

		foreach (explode('|', $methods) as $method) {
            self::$beforeRoutes[$method][] = array(
                'pattern' => $pattern,
                'fn' => $fn
            );
        }
	}

	/**
	 * Store a route and a handling function to be executed when accessed using one of the specified methods
	 * 
	 * @param string $methods
	 * @param string $pattern
	 * @param object|callable $fn
	 */
	public static function match($methods, $pattern, $fn, $params = [])
	{
		$pattern = self::$baseRoute . '/' . trim($pattern, '/');
        $pattern = self::$baseRoute ? rtrim($pattern, '/') : $pattern;

        if (is_callable($fn))
        	$closure = $fn;
        elseif (stripos($fn, '@') !== false) {

        	// Set Namespace if exist
        	if (array_key_exists('namespace', $params))
        		$closure = 'App\\Controllers\\' . $params['namespace'] . '\\' . $fn;
        	else
        		$closure = 'App\\Controllers\\' . $fn;

        }

        foreach (explode('|', $methods) as $method) {

        	// Set Middleware if exist
        	if (array_key_exists('middleware', $params)) {
        		foreach ($params['middleware'] as $middleware) {
        			self::$beforeRoutes[$method][] = [
						'pattern'	=> $pattern,
						'fn'		=> 'App\\Middlewares\\' . $middleware . '@handle'
					];
        		}
        	}

            self::$afterRoutes[$method][] = array(
                'pattern' => $pattern,
                'fn' => $closure
            );
        }
	}

	/**
	 * Shorthand for a route accessed using any method
	 *
	 * @param string $pattern
	 * @param string $fn
	 */
	public static function all($pattern, $fn, $params = [])
	{
		self::match('GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD', $pattern, $fn, $params);
	}

	/**
	 * Shorthand for a route accessed using GET
	 *
	 * @param string $pattern
	 * @param object|callable $fn
	 */
	public static function get($pattern, $fn, $params = [])
	{
		self::match('GET', $pattern, $fn, $params);
	}

	/**
	 * Shorthand for a route accessed using POST
	 *
	 * @param string $pattern
	 * @param object|callable $fn
	 */
	public static function post($pattern, $fn, $params = [])
	{
		self::match('POST', $pattern, $fn, $params);
	}

	/**
	 * Shorthand for a route accessed using PATCH
	 *
	 * @param string $pattern
	 * @param object|callable $fn
	 */
	public static function patch($pattern, $fn, $params = [])
	{
		self::match('PATCH', $pattern, $fn, $params);
	}

	/**
	 * Shorthand for a route accessed using DELETE
	 *
	 * @param string $pattern
	 * @param object|callable $fn
	 */
	public static function delete($pattern, $fn, $params = [])
	{
		self::match('DELETE', $pattern, $fn, $params);
	}

	/**
	 * Shorthand for a route accessed using PUT
	 *
	 * @param string $pattern
	 * @param object|callable $fn
	 */
	public static function put($pattern, $fn, $params = [])
	{
		self::match('PUT', $pattern, $fn, $params);
	}

	/**
	 * Shorthand for a route accessed using OPTIONS
	 *
	 * @param string $pattern
	 * @param object|callable $fn
	 */
	public static function options($pattern, $fn, $params = [])
	{
		self::match('OPTIONS', $pattern, $fn, $params);
	}

	/**
	 * Mounts a collection of callbacks onto a base route
	 *
	 * @param string $baseRoute
	 * @param callable $fn
	 */
	public static function group($baseRoute, $fn, $params = [])
	{
		// Track current base route
		$curBaseRoute = self::$baseRoute;

		// Build new base route string
		self::$baseRoute .= $baseRoute;

		// Call the callable
		call_user_func($fn);

		// Restore original base route
		self::$baseRoute = $curBaseRoute;

		// Set Middlewares
		if (!empty($params)) {

			// If namespace defined
			if (array_key_exists('namespace', $params)) {
				$methods = 'GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD';

				foreach (explode('|', $methods) as $method) {

					if (array_key_exists($method, self::$afterRoutes)) {

						foreach (self::$afterRoutes[$method] as $key => $value) {

							$patternExists = strpos($value['pattern'], $baseRoute);

							if ($patternExists !== false) {
								$fnParts 			= explode('App\Controllers', $value['fn']);
								$fnWithNameSpace	= 'App\Controllers\\' . $params['namespace'] . $fnParts[1];
								self::$afterRoutes[$method][$key]['fn'] = $fnWithNameSpace;
							}
							
						}

					}

				}

			}

			// If middleware defined
			if (array_key_exists('middleware', $params)) {
				$methods = 'GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD';

				foreach ($params['middleware'] as $middleware) {
					
					foreach (explode('|', $methods) as $method) {
						self::$beforeRoutes[$method][] = [
							'pattern'	=> $baseRoute,
							'fn'		=> 'App\\Middlewares\\' . $middleware . '@handle'
						];

						self::$beforeRoutes[$method][] = [
							'pattern'	=> $baseRoute . '/.*',
							'fn'		=> 'App\\Middlewares\\' . $middleware . '@handle'
						];
					}

				}
			}

		}
	}

	/**
	 * Middleware
	 *
	 * @param string
	 * @return void
	 */
	public static function middleware($methods, $pattern, $fn)
	{
		$pattern = self::$baseRoute . '/' . trim($pattern, '/');
		$pattern = self::$baseRoute ? rtrim($pattern, '/') : $pattern;

		foreach (explode('|', $methods) as $method) {
            self::$beforeRoutes[$method][] = array(
                'pattern' => $pattern,
                'fn' => 'App\\Middlewares\\' . $fn . '@handle'
            );
        }
	}

	/**
	 * Get all request headers
	 *
	 * @return array
	 */
	public static function getRequestHeaders()
	{
		// If getallheaders() is available, use that
		if (function_exists('getallheaders')) {
            return getallheaders();
        }

        // Method getallheaders() not available: manually extract 'm
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                $headers[str_replace(array(' ', 'Http'), array('-', 'HTTP'), ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
	}

	/**
	 * Get the request method used, taking overrides into account
	 *
	 * @return string
	 */
	public static function getRequestMethod()
	{
		// Take the method as found in $_SERVER
		$method = $_SERVER['REQUEST_METHOD'];

		// If it's a HEAD request override it to being GET and prevent any output, as per HTTP Specification
		if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_start();
            $method = 'GET';
        } 
        // If it's a POST request, check for a method override header
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $headers = self::getRequestHeaders();
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], array('PUT', 'DELETE', 'PATCH'))) {
                $method = $headers['X-HTTP-Method-Override'];
            }
        }

        return $method;
	}

	/**
	 * Execute the router: Loop all defined before middleware's and routes, and execute the handling function if a match was found
	 *
	 * @param object|callable $callback
	 * @return bool
	 */
	public static function run($callback = null)
	{
		// Define which method we need to handle
		self::$requestedMethod = self::getRequestMethod();

		// Handle all before middlewares
		if (isset(self::$beforeRoutes[self::$requestedMethod])) {
            self::handle(self::$beforeRoutes[self::$requestedMethod]);
        }

        // Handle all routes
        $numHandled = 0;
        if (isset(self::$afterRoutes[self::$requestedMethod])) {
            $numHandled = self::handle(self::$afterRoutes[self::$requestedMethod], true);
        }

        // If no route was handled, trigger the 404 (if any)
        if ($numHandled === 0) {
            if (self::$notFoundCallback && is_callable(self::$notFoundCallback)) {
                call_user_func(self::$notFoundCallback);
            } else {
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
                throw new ExceptionHandler("Hata", "Controller bulunamadı");
                
            }
        } 
        // If a route was handled, perform the finish callback (if any)
        else {
            if ($callback) {
                $callback();
            }
        }

        // If it originally was a HEAD request, clean up after ourselves by emptying the output buffer
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_end_clean();
        }

        // Return true if a route was handled, false otherwise
        if ($numHandled === 0) {
            return false;
        }

        return true;
	}

	/**
	 * Set the 404 handling function
	 *
	 * @param object|callable $fn
	 */
	public static function set404($fn)
	{
		self::$notFoundCallback = $fn;
	}

	/**
	 * List all routes
	 *
	 * @return string
	 */
	public static function listRoutes()
	{
		return dd(self::$afterRoutes);
	}

	/**
	 * Handle a set of routes: if a match is found, execute the relating handling function
	 *
	 * @param array $routes
	 * @param boolean $quitAfterRun
	 * @return int
	 */
	private static function handle($routes, $quitAfterRun = false)
	{
		// Counter to keep track of the number of routes we've handled
		$numHandled = 0;

		// The current page URL
		$uri = self::getCurrentUri();

		// Loop all routes
		foreach ($routes as $route) {

			// we have a match!
			if (preg_match_all('#^' . $route['pattern'] . '$#', $uri, $matches, PREG_OFFSET_CAPTURE)) {
				// Rework matches to only contain the matches, not the orig string
				$matches = array_slice($matches, 1);

				// Extract the matched URL parameters (and only the parameters)
				$params = array_map(function ($match, $index) use ($matches) {
					// We have a following parameter: take the substring from the current param position until the next one's position
					if (isset($matches[$index + 1]) && isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                        return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
                    } 
                    // We have no following parameters: return the whole lot
                    else {
                    	return (isset($match[0][0]) ? trim($match[0][0], '/') : null);
                    }
				}, $matches, array_keys($matches));

				// Call the handling function with the URL parameters if the desired input is callable
				if (is_callable($route['fn'])) {
                    call_user_func_array($route['fn'], $params);
                } 
                // if not, check the existence of special parameters
                elseif (stripos($route['fn'], '@') !== false) {
                	// explode segments of given route
                	list($controller, $method) = explode('@', $route['fn']);

                	// check if class exists, if not just ignore.
                	if (class_exists($controller)) {
                		// first check if is a static method, directly trying to invoke it. if isn't a valid static method, we will try as a normal method invocation.
                		if (call_user_func_array(array(new $controller, $method), $params) === false) {
                			// try call the method as an non-static method. (the if does nothing, only avoids the notice)
                			if (forward_static_call_array(array($controller, $method), $params) === false) ;
                		}
                	}
                }

                $numHandled++;

                // If we need to quit, then quit
                if ($quitAfterRun) {
                    break;
                }
			}

		}

		// Return the number of routes handled
		return $numHandled;
	}

	/**
	 * Define the current relative URI
	 *
	 * @return string
	 */
	protected static function getCurrentUri()
	{
		// Get the current Request URI and remove rewrite base path from it (= allows one to run the router in a sub folder)
		$uri = substr($_SERVER['REQUEST_URI'], strlen(self::getBasePath()));

		// Don't take query params into account on the URL
		if (strstr($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        // Remove trailing slash + enforce a slash at the start
        return '/' . trim($uri, '/');
	}

	/**
	 * Return server base Path, and define it if isn't defined.
	 *
	 * @return string
	 */
	protected static function getBasePath()
	{
		// Check if server base path is defined, if not define it.
		if (null === self::$serverBasePath) {
            self::$serverBasePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        }

        return self::$serverBasePath;
	}

}