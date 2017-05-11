<?php
/*************************************************
 * Titan-2 Mini Framework
 * Helpers
 *
 * Author   : Turan Karatuğ
 * Web      : http://www.titanphp.com
 * Docs     : http://kilavuz.titanphp.com 
 * Github   : http://github.com/tkaratug/titan2
 * License  : MIT   
 *
 *************************************************/

/**
 * Debug Helper
 */
if (!function_exists('dd')) {
	function dd($data, $stop = false)
	{
		echo '<pre>';
		print_r($data);
		echo '</pre>';

		if ($stop === true)
			die();
	}
}

/**
 * Get current language
 *
 * @return string
 */
if (!function_exists('get_lang')) {
    function get_lang()
    {
        $lang = Config::get('app', 'general', 'default_lang');

        if (!Session::has(md5('lang'))) {
            Session::set(md5('lang'), $lang);
            return $lang;
        } else {
            return Session::get(md5('lang'));
        }
    }
}

/**
 * Set language
 *
 * @param string $lang
 * @return void
 */
if (!function_exists('set_lang')) {
    function set_lang($lang = '')
    {
        $language = Config::get('app', 'general', 'default_lang');

        if (!is_string($lang))
        	return false;

        if (empty($lang))
        	$lang = $language;

        Session::set(md5('lang'), $lang);
    }
}

/**
 * Get string with current language
 *
 * @param string $file
 * @param string $key
 * @param string $change
 * @return string
 */
if ( ! function_exists('lang') ) {
    function lang($file = '', $key = '', $change = '')
    {
        global $lang;

        $config = Config::get('app', 'general', 'languages');

        if (!is_string($file) || !is_string($key))
        	return false;

        $appLangDir = APP_DIR . 'Languages/' . ucwords($config[get_lang()]) . '/' . ucwords($file) . '.php';
        $sysLangDir = SYSTEM_DIR . 'Languages/' . ucwords($config[get_lang()]) . '/' . ucwords($file) . '.php';

        if (file_exists($appLangDir))
            require_once $appLangDir;
        elseif (file_exists($sysLangDir))
            require_once $sysLangDir;
        else
        	throw new System\Libs\Exception\ExceptionHandler('Dosya bulunamadı', '<b>Language : </b> ' . $file);

        $zone = strtolower($file);

        if (array_key_exists($key, $lang[$zone])) {
            $str = $lang[$zone][$key];

            // Change special words
            if (!is_array($change)) {
                if (!empty($change)) {
                    return str_replace('%s', $change, $str);
                } else {
                    return $str;
                }
            } else {
                if (!empty($change)) {
                    $keys = [];
                    $vals = [];

                    foreach($change as $key => $value) {
                        $keys[] = $key;
                        $vals[] = $value;
                    }

                    return str_replace($keys, $vals, $str);
                } else {
                    return $str;
                }
            }

        } else {
            return false;
        }
    }
}

/**
 * Redirect to specified url
 *
 * @param string $url
 * @param integer $delay
 * @return void
 */
if (!function_exists('redirect')) {
    function redirect($url, $delay = 0)
    {
        if ($delay > 0)
            header("Refresh:" . $delay . ";url=" . $url);
        else
            header("Location:" . $url);
    }
}

/**
 * Define csrf token
 *
 * @return string
 */
if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        Session::set('titan_token', base64_encode(openssl_random_pseudo_bytes(32)));
        return Session::get('titan_token');
    }
}

/**
 * Validate csrf token
 *
 * @param string $token
 * @return boolean
 */
if (!function_exists('csrf_check')) {
    function csrf_check($token)
    {
        if (Session::has('titan_token') && $token == Session::get('titan_token')) {
            Session::delete('titan_token');
            return true;
        }

        return false;
    }
}

/**
 * Get assets
 *
 * @param string $file
 * @return string
 */
if (!function_exists('get_asset')) {
    function get_asset($file)
    {       
        if (file_exists(ROOT_DIR . 'Public/Resources/' . $file))
            return RESOURCES_DIR . $file;
        else
            throw new System\Libs\Exception\ExceptionHandler('Dosya bulunamadı', '<b>Asset : </b> ' . $file);
    }
}

/**
 * Get base url
 *
 * @param string $url
 * @return string
 */
if (!function_exists('base_url')) {
    function base_url($url = null)
    {
        if (stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true)
            $protocol = 'https';
        else
            $protocol = 'http';

        if (is_null($url))
            return $protocol . "://" . $_SERVER['HTTP_HOST'];
        else
            return $protocol . "://" . rtrim($_SERVER['HTTP_HOST'], '/') . '/' . $url;
    }
}

/**
 * Get current url
 *
 * @return string
 */
if (!function_exists('current_url')) {
    function current_url()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}