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
namespace System\Libs\Console;

use System\Libs\Router\Router;

class Console
{
    // Params
    private $params;

    // Foreground Colors
    private $foreground_colors = [];

    // Background Colors
    private $background_colors = [];

    public function __construct()
    {
        // Setting up shell colors
		$this->foreground_colors['black']         = '0;30';
		$this->foreground_colors['dark_gray']     = '1;30';
		$this->foreground_colors['blue']          = '0;34';
		$this->foreground_colors['light_blue']    = '1;34';
		$this->foreground_colors['green']         = '0;32';
		$this->foreground_colors['light_green']   = '1;32';
		$this->foreground_colors['cyan']          = '0;36';
		$this->foreground_colors['light_cyan']    = '1;36';
		$this->foreground_colors['red']           = '0;31';
		$this->foreground_colors['light_red']     = '1;31';
		$this->foreground_colors['purple']        = '0;35';
		$this->foreground_colors['light_purple']  = '1;35';
		$this->foreground_colors['brown']         = '0;33';
		$this->foreground_colors['yellow']        = '1;33';
		$this->foreground_colors['light_gray']    = '0;37';
		$this->foreground_colors['white']         = '1;37';

		$this->background_colors['black']         = '40';
		$this->background_colors['red']           = '41';
		$this->background_colors['green']         = '42';
		$this->background_colors['yellow']        = '43';
		$this->background_colors['blue']          = '44';
		$this->background_colors['magenta']       = '45';
		$this->background_colors['cyan']          = '46';
		$this->background_colors['light_gray']    = '47';
    }

    /**
     * Run command
     * @param array $params
     * @return string
     */
    public function run($params)
    {
        $this->params = $params;

        if (!$this->params) {
            return $this->help();
        } else {
            if ($params[0] == '-h') {
                return $this->help();
            } else if ($params[0] == '-v') {
                return $this->version();
            } else if (strpos($params[0], ':') !== false) {
                $slice = explode(':', $params[0]);

                if ($slice[0] == 'make') {
                    if (array_key_exists(1, $params))
                        return $this->make($slice[1], $params[1]);
                    else
                        return $this->make($slice[1]);
                } else if ($slice[0] == 'clear') {
                    return $this->clear($slice[1]);
                } else {
                    return $this->getColoredString('Gecersiz komut. "' . $params[0] . '"', 'white', 'red');
                }
            } else {
                return $this->getColoredString('Gecersiz komut. "' . $params[0] . '"', 'white', 'red');
            }
        }
    }

    /**
     * Returns help document
     *
     * @return string
     */
    private function help()
    {
        return $this->getColoredString('// ========== [+] Titan Web Framework Console Komutlari ========== //', 'red') . "\n\n" .
               $this->getColoredString('- Make', 'yellow') . "\n" .
               $this->getColoredString('[make:controller]', 'light_blue') . "\t" . $this->getColoredString('Controller olusturmak icin kullanilir. (Orn: make:controller MyController).') . "\n" .
               $this->getColoredString('[make:model]', 'light_blue') . "\t\t" . $this->getColoredString('Model olusturmak icin kullanilir. (Orn: make:model MyModel).') . "\n" .
               $this->getColoredString('[make:middleware]', 'light_blue') . "\t" . $this->getColoredString('Middleware olusturmak icin kullanilir. (Orn: make:middleware MyMiddleware).') . "\n" .
               $this->getColoredString('[make:listener]', 'light_blue') . "\t\t" . $this->getColoredString('Listener olusturmak icin kullanilir. (Orn: make:listener MyListener).') . "\n" .
               $this->getColoredString('[make:key]', 'light_blue') . "\t\t" . $this->getColoredString('128 Bit Key olusturmak icin kullanilir.') . "\n\n" .

               $this->getColoredString('- Clear', 'yellow') . "\n" .
               $this->getColoredString('[clear:cache]', 'light_blue') . "\t\t" . $this->getColoredString('/App/Storage/Cache dizinini temizlemek icin kullanilir.') . "\n" .
               $this->getColoredString('[clear:logs]', 'light_blue') . "\t\t" . $this->getColoredString('/App/Storage/Logs dizinini temizlemek icin kullanilir.') . "\n" .

               $this->getColoredString('[-v]', 'light_blue') . "\t\t\t" . $this->getColoredString('Titan Web Framework versiyon bilgisini verir.') . "\n" .
               $this->getColoredString('[-h]', 'light_blue') . "\t\t\t" . $this->getColoredString('Tum console komutlari ile ilgili bilgi verir.') . "\n\n" .
               $this->getColoredString('// ========== [-] Titan Web Framework Console Komutlari ========== //', 'red');
    }

    /**
     * Get version of framework
     *
     * @return string
     */
    private function version()
    {
        return $this->getColoredString('Titan Web Framework: ', 'light_red') . "\t" . $this->getColoredString('v' . VERSION, 'light_blue');
    }

    /**
     * Run make commands
     *
     * @param string $command
     * @param string $params
     * @return string
     */
    private function make($command, $params = null)
    {
        switch ($command) {
            case 'controller'   : return $this->createController($params); break;
            case 'model'        : return $this->createModel($params); break;
            case 'middleware'   : return $this->createMiddleware($params); break;
            case 'listener'     : return $this->createListener($params); break;
            case 'key'          : return $this->generateCode(); break;
            default             : return $this->getColoredString('"make" komutu icin gecersiz parametre. "' . $params . '"', 'white', 'red');
        }
    }

    /**
     * Run clear commands
     *
     * @param string $params
     * @return string
     */
    private function clear($params)
    {
        switch ($params) {
            case 'cache'        : return $this->clearCache(); break;
            case 'logs'         : return $this->clearLogs(); break;
            default             : return $this->getColoredString('"clear" komutu icin gecersiz parametre. "' . $params . '"', 'white', 'red');
        }
    }

    /**
     * Create Controller
     *
     * @param string $controller
     * @return string
     */
    private function createController($controller)
    {
        $isDir          = strpos($controller, '/');

        if ($isDir) {
            $parts      = explode('/', $controller);
            $class      = end($parts);
            array_pop($parts);
            $namespace  = 'App\\Controllers\\' . implode('\\', $parts);
            $location   = 'App/Controllers/' . implode('/', $parts);
        } else {
            $namespace  = 'App\\Controllers';
            $class      = $controller;
            $location   = 'App/Controllers/' . $controller;
        }

        $file = "{$location}/{$class}.php";

        if (file_exists($file)) {
            return $this->getColoredString('Controller zaten mevcut:', 'red') . "\t" . $this->getColoredString($file);
        } else {
            $this->makeDir($location);
            $location   = $location . '/' . $class . '.php';
            $file       = fopen ($location, 'w');
            $content    = "<?php\nnamespace $namespace;\n\nuse System\\Kernel\\Controller;\nuse View;\n\nclass $class extends Controller\n{\n\n\tpublic function index()\n\t{\n\t\t\n\t}\n\n}";
            fwrite ($file, $content);
            fclose($file);

            return $this->getColoredString('Controller basariyla olusturuldu: ', 'light_blue') . "\t" . $this->getColoredString($location);
        }
    }

    /**
     * Create Model
     *
     * @param string $model
     * @return string
     */
    private function createModel($model)
    {
        $isDir          = strpos($model, '/');

        if ($isDir) {
            $parts      = explode('/', $model);
            $class      = end($parts);
            array_pop($parts);
            $namespace  = 'App\\Models\\' . implode('\\', $parts);
            $location   = 'App/Models/' . implode('/', $parts);
        } else {
            $namespace  = 'App\\Models';
            $class      = $model;
            $location   = 'App/Models';
        }

        $file = "{$location}/{$class}.php";

        if (file_exists($file))
            return $this->getColoredString('Model zaten mevcut:', 'red') . "\t" . $this->getColoredString($file);

        $this->makeDir($location);
        $location   = $location . '/' . $class . '.php';
        $file       = fopen ($location, 'w');
        $content    = "<?php\nnamespace $namespace;\n\nuse DB;\n\nclass $class\n{\n\n\t\n\n}";
        fwrite ($file, $content);
        fclose($file);

        return $this->getColoredString('Model basariyla olusturuldu: ', 'light_blue') . "\t" . $this->getColoredString($location);
    }

    /**
     * Create Middleware
     *
     * @param string $middleware
     * @return string
     */
    private function createMiddleware($middleware)
    {
        $namespace  = 'App\\Middlewares';
        $class      = $middleware;

        $location   = 'App/Middlewares/' . $middleware . '.php';

        if (file_exists($location))
            return $this->getColoredString('Middleware zaten mevcut:', 'red') . "\t" . $this->getColoredString($location);

        $file       = fopen ($location, 'w');
        $content    = "<?php\nnamespace $namespace;\n\nclass $class\n{\n\n\tpublic static function handle()\n\t{\n\t\t\n\t}\n\n}";
        fwrite ($file, $content);
        fclose($file);

        return $this->getColoredString('Middleware basariyla olusturuldu: ', 'light_blue') . "\t" . $this->getColoredString($location);
    }

    /**
     * Create Listener
     *
     * @param string $listener
     * @return string
     */
    private function createListener($listener)
    {
        $namespace  = 'App\\Listeners';
        $class      = $listener;

        $location   = 'App/Listeners/' . $listener . '.php';

        if (file_exists($location))
            return $this->getColoredString('Listener zaten mevcut:', 'red') . "\t" . $this->getColoredString($location);

        $file       = fopen ($location, 'w');
        $content    = "<?php\nnamespace $namespace;\n\nclass $class\n{\n\n\tpublic function handle()\n\t{\n\t\t\n\t}\n\n}";
        fwrite ($file, $content);
        fclose($file);

        return $this->getColoredString('Listener basariyla olusturuldu: ', 'light_blue') . "\t" . $this->getColoredString($location);
    }

    /**
     * Generate 128-bit Key
     *
     * @param int $length
     * @param boolean $strong
     */
    private function generateCode($length = 24, $strong = true)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $token = base64_encode(openssl_random_pseudo_bytes($length, $strong));
            if($strong == true)
                return $this->getColoredString('128 Bit Key olusturuldu: ', 'light_cyan') . "\t" . $this->getColoredString(strtr(substr($token, 0, $length), '+/=', '-_,'));
        } else {
            $characters = '0123456789';
            $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz/+';
            $charactersLength = strlen($characters)-1;
            $token = '';

            for ($i = 0; $i < $length; $i++) {
                $token .= $characters[mt_rand(0, $charactersLength)];
            }

            return $this->getColoredString('128-bit key olusturuldu: ', 'light_blue') . "\t" . $this->getColoredString($token);
        }
    }

    /**
     * Clear Cache
     *
     * @return string
     */
    private function clearCache()
    {
        array_map('unlink', glob("App/Storage/Cache/*"));
        return $this->getColoredString('Cache dizini bosaltildi.', 'light_blue');
    }

    /**
     * Clear Logs
     *
     * @return string
     */
    private function clearLogs()
    {
        array_map('unlink', glob("App/Storage/Logs/*"));
        return $this->getColoredString('Logs dizini bosaltildi.', 'light_blue');
    }

    /**
     * Returns colored string
     * @param string $string
     * @param string $foreground_color
     * @param string $background_color
     * @return string
     */
	private function getColoredString($string, $foreground_color = null, $background_color = null) {
		$colored_string = "";

		// Check if given foreground color found
		if (isset($this->foreground_colors[$foreground_color])) {
			$colored_string .= "\e[" . $this->foreground_colors[$foreground_color] . "m";
        }
        
		// Check if given background color found
		if (isset($this->background_colors[$background_color])) {
			$colored_string .= "\e[" . $this->background_colors[$background_color] . "m";
		}

		// Add string and end coloring
		$colored_string .=  $string . "\e[0m";

		return $colored_string . "\n";
	}

    /**
     * Get foreground colors
     * @return array
     */
	private function getForegroundColors() {
		return array_keys($this->foreground_colors);
	}

	/**
     * Get background colors
     * @return array
     */
	private function getBackgroundColors() {
		return array_keys($this->background_colors);
    }
    
    /**
     * Create directories recursively
     * @param string $path
     * @param int $permissions
     * @return bool
     */
    private function makeDir($path, $permissions = 0755) {
        return is_dir($path) || mkdir($path, $permissions, true);
    }

}
