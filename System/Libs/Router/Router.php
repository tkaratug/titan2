<?php
/*************************************************
 * Titan Router
 * PHP Routing Library
 *
 * Author   : Turan Karatuğ
 * Github   : http://github.com/tkaratug/titan-router
 * License  : MIT
 *
 *************************************************/
namespace System\Libs\Router;

use System\Libs\Exception\ExceptionHandler;

class Router
{
    // Routes
    private static $routes      = [];

    // Middlewares
    private static $middlewares = [];

    // Base Route
    private static $baseRoute   = '/';

    // Namespace
    private static $namespace   = '';

    // Domain
    private static $domain      = '';

    // Not Found Callback
    private static $notFound    = '';

    // Groups
    private static $groups      = [];
    
    // Group Counter
    private static $groupped    = 0;

    // Patterns
    private static $patterns    = [
        '{all}'     => '([^/]+)',
        '{num}'     => '([0-9]+)',
        '{alpha}'   => '([a-zA-Z]+)',
        '{alnum}'   => '([a-zA-Z0-9_-]+)'
    ];

    // Namespaces
    private static $namespaces  = [
        'controllers'   => 'App\\Controllers',
        'middlewares'   => 'App\\Middlewares'
    ];

    private function __construct() {}
    private function __clone() {}

    /**
     * Routing Groups
     *
     * @param callable $callback
     */
    public static function group($callback)
    {
        self::$groupped++;

        self::$groups[] = [
            'baseRoute'     => self::$baseRoute,
            'middlewares'   => self::$middlewares,
            'namespace'     => self::$namespace,
            'domain'        => self::$domain
        ];

        // Call the Callable
        call_user_func($callback);

        self::$groupped--;

        if (self::$groupped > 0) {
            self::$baseRoute    = self::$groups[self::$groupped-1]['baseRoute'];
            self::$middlewares  = self::$groups[self::$groupped-1]['middlewares'];
            self::$namespace    = self::$groups[self::$groupped-1]['namespace'];
            self::$domain       = self::$groups[self::$groupped-1]['domain'];
        } else {
            // Reset Base Route
            self::$baseRoute    = '/';

            // Reset Middlewares
            self::$middlewares  = [];

            // Reset Namespace
            self::$namespace    = '';

            // Reset Domain
            self::$domain       = '';

            // Reset Group Counter
            self::$groupped     = 0;
        }
    }

    /**
     * Defining Namespace
     *
     * @param string $namespace
     */
    public static function setNamespace($namespace)
    {
        // Set Namespace
        self::$namespace = $namespace;

        return new self;
    }

    /**
     * Defining Middlewares
     *
     * @param array $middlewares
     */
    public static function middleware($middlewares)
    {
        foreach ($middlewares as $middleware) {
            self::$middlewares[$middleware] = [
                'callback'  => self::$namespaces['middlewares'] . '\\' . ucfirst($middleware) . '@handle'
            ];
        }

        return new self;
    }

    /**
     * Defining Prefix
     *
     * @param string $prefix
     */
    public static function prefix($prefix)
    {
        // Set Base Route
        self::$baseRoute    = '/' . $prefix;

        return new self;
    }

    /**
     * Defining Domain
     *
     * @param string $domain
     */
    public static function domain($domain)
    {
        self::$domain = $domain;
        return new self;
    }

    /**
     * Add Route
     *
     * @param string $method
     * @param string $pattern
     * @param string|callable $callback
     */
    public static function route($method, $pattern, $callback)
    {
        if ($pattern == '/')
            $pattern    = self::$baseRoute . trim($pattern, '/');
        else {
            if (self::$baseRoute == '/')
                $pattern    = self::$baseRoute . trim($pattern, '/');
            else
                $pattern    = self::$baseRoute . $pattern;
        }

        $uri        = $pattern;
        $pattern    = str_replace(array_keys(self::$patterns), array_values(self::$patterns), $pattern);
        $pattern    = '/^' . str_replace('/', '\/', $pattern) . '$/';

        if (is_callable($callback)) {
            $closure = $callback;
        } elseif (stripos($callback, '@') !== false) {
            if (self::$namespace)
                $closure = self::$namespaces['controllers'] . '\\' . ucfirst(self::$namespace) . '\\' . $callback;
            else
                $closure = self::$namespaces['controllers'] . '\\' . $callback;
        }

		$routeArray = [
			'uri'       => $uri,
			'method'    => $method,
			'pattern'   => $pattern,
			'callback'  => $closure
		];

		if (self::$namespace)
			$routeArray['namespace']    = ucfirst(self::$namespace);

		if (!empty(self::$middlewares))
			$routeArray['middlewares']  = self::$middlewares;

		if (self::$domain)
			$routeArray['domain']       = self::$domain;

		self::$routes[] = $routeArray;
    }

    /**
     * Execute Routing
     */
    public static function run()
    {
        $matched        = 0;
        $methodCheck    = true;
        $domainCheck    = true;

        foreach (self::$routes as $key => $val) {

            if (preg_match($val['pattern'], self::getCurrentUri(), $params)) {

                // Checking domain
                if (array_key_exists('domain', $val)) {
                    if ($val['domain'] !== trim(str_replace('www.', '', $_SERVER['SERVER_NAME']), '/')) {
                        $domainCheck = false;
                    } else {
                        $domainCheck = true;
                    }
                }

                // Checking request method
                if ($val['method'] !== self::getRequestMethod()) {
                    $methodCheck = false;
                } else {
                    $methodCheck = true;
                }

                if ($domainCheck && $methodCheck) {
                    $matched++;

                    array_shift($params);

                    // Checking middlewares
                    if (array_key_exists('middlewares', $val)) {
                        foreach ($val['middlewares'] as $midKey => $midVal) {
                            list($controller, $method) = explode('@', $midVal['callback']);

                            if (class_exists($controller)) {
                                call_user_func_array([new $controller, $method], []);
                            }
                        }
                    }

                    if (is_callable($val['callback'])) {
                        call_user_func_array($val['callback'], array_values($params));
                    } else if (stripos($val['callback'], '@') !== false) {
                        list($controller, $method) = explode('@', $val['callback']);

                        if (class_exists($controller)) {
                            call_user_func_array([new $controller, $method], array_values($params));
                        } else {
                            self::pageNotFound();
                        }
                    }

                    break;
                }
                
            }

        }

        if ($matched === 0)
            self::pageNotFound();
    }

    /**
     * Page Not Found Redirection
     */
    private static function pageNotFound()
    {
        if (self::$notFound && is_callable(self::$notFound)) {
            call_user_func(self::$notFound);
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            throw new ExceptionHandler("Hata", "Controller bulunamadı");
        }
    }

    /**
     * Get Current URI
     *
     * @return string
     */
    public static function getCurrentUri()
    {
        // Get the current Request URI and remove rewrite base path from it
        $uri = substr($_SERVER['REQUEST_URI'], strlen(self::getBasePath()));

        // Don't take query params into account on the URL
        if (strstr($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        // Remove trailing slash + enforce a slash at the start
        return '/' . trim($uri, '/');
    }

    /**
     * Get Base Path
     *
     * @return string
     */
    public static function getBasePath()
    {
        $scriptName = array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1);
        return implode('/', $scriptName) . '/';
    }

    /**
     * Get All Request Headers
     *
     * @return array
     */
    public static function getRequestHeaders()
    {
        // If getallheaders() is available, use that
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        // If getallheaders() is not available, use that
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                $headers[str_replace(array(' ', 'Http'), array('-', 'HTTP'), ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }

    /**
     * Get Request Method
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
     * Add a Route Using Get Method
     *
     * @param string $pattern
     * @param string|callable $callback
     */
    public static function get($pattern, $callback)
    {
        self::route('GET', $pattern, $callback);
        return new self;
    }

    /**
     * Add a Route Using Post Method
     *
     * @param string $pattern
     * @param string|callable $callback
     */
    public static function post($pattern, $callback)
    {
        self::route('POST', $pattern, $callback);
    }

    /**
     * Add a Route Using Patch Method
     *
     * @param string $pattern
     * @param string|callable $callback
     */
    public static function patch($pattern, $callback)
    {
        self::route('PATCH', $pattern, $callback);
    }

    /**
     * Add a Route Using Delete Method
     *
     * @param string $pattern
     * @param string|callable $callback
     */
    public static function delete($pattern, $callback)
    {
        self::route('DELETE', $pattern, $callback);
    }

    /**
     * Add a Route Using Put Method
     *
     * @param string $pattern
     * @param string|callable $callback
     */
    public static function put($pattern, $callback)
    {
        self::route('PUT', $pattern, $callback);
    }

    /**
     * Add a Route Using Options Method
     *
     * @param string $pattern
     * @param string|callable $callback
     */
    public static function options($pattern, $callback)
    {
        self::route('OPTIONS', $pattern, $callback);
    }
	
	/**
	 * Add a Route Using Multiple Methods
	 *
	 * @param array $methods
	 * @param string $pattern
	 * @param string|callable $callback
	 */
	public static function match($methods, $pattern, $callback)
	{
		foreach ($methods as $method) {
			self::route(strtoupper($method), $pattern, $callback);
		}
	}

    /**
     * List All Routes
     *
     * @return array
     */
    public static function getRoutes()
    {
        return self::$routes;
    }

    /**
     * Set the 404 handling function
     *
     * @param object|callable $callback
     */
    public static function set404($callback)
    {
        self::$notFound = $callback;
    }

    public static function __callStatic($method, $args)
    {
        if ($method == 'namespace') {
            self::setNamespace($args[0]);
            return new self;
        }
    }

    public function __call($method, $args)
    {
        if ($method == 'namespace') {
            self::setNamespace($args[0]);
            return new self;
        }
    }
}