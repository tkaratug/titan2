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
        $lang = config('app.general.default_lang');

        if (Session::has(md5('lang')))
            return Session::get(md5('lang'));

        Session::set(md5('lang'), $lang);

        return $lang;
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
        $language = config('app.general.default_lang');

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
if (!function_exists('lang') ) {
    function lang($file = '', $key = '', $change = '')
    {
        global $lang;

        $config = config('app.general.languages');

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

                if (!empty($change))
                    return str_replace('%s', $change, $str);

                return $str;
            }

            if (!empty($change)) {

                $keys = [];
                $vals = [];

                foreach($change as $key => $value) {
                    $keys[] = $key;
                    $vals[] = $value;
                }

                return str_replace($keys, $vals, $str);
            }

            return $str;

        }

        return false;
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
    function get_asset($file, $version = null)
    {
        if (!file_exists(ROOT_DIR . '/Public/' . $file))
            throw new System\Libs\Exception\ExceptionHandler('Dosya bulunamadı', '<b>Asset : </b> ' . $file);

        return (is_null($version)) ? PUBLIC_DIR . $file : PUBLIC_DIR . $file . '?' . $version;
    }
}

/**
 * Get files inside Public directory
 *
 * @param string|null $file
 * @return string
 */
if (!function_exists('public_path')) {
    function public_path($file = null)
    {
        if ($file !== null) {
            if (!file_exists(ROOT_DIR . '/Public/' . $file))
                throw new System\Libs\Exception\ExceptionHandler('Dosya bulunamadı', '<b>Public : </b> ' . $file);

            return ROOT_DIR . '/Public/' . $file;
        }
        
        return ROOT_DIR . '/Public/';
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
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            $protocol = 'https';
        else
            $protocol = 'http';

        if (is_null($url))
            return $protocol . "://" . $_SERVER['HTTP_HOST'];

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

/**
 * Make internal link
 *
 * @return string
 */
if (!function_exists('link_to')) {
    function link_to($url)
    {
        return BASE_DIR . '/' . $url;
    }
}

/**
 * Reach All Request Data
 *
 * @param string|array/null $params
 * @return array|string
 */
if (!function_exists('request')) {
    function request($params = null)
    {
        $requestMethod = Request::getRequestMethod();

        switch ($requestMethod) {
            case 'GET'      : $request = Request::get(); break;
            case 'POST'     : $request = Request::post(); break;
            case 'PUT'      : $request = Request::put(); break;
            case 'PATCH'    : $request = Request::patch(); break;
            case 'DELETE'   : $request = Request::delete(); break;
            default         : $request = Request::all();
        }

        if (is_null($params))
            return $request;

        if (is_array($params)) {
            foreach ($params as $param) {
                $data[$param] = $request[$param];
            }
            return $data;
        }

        return $request[$params];
    }
}

/**
 * Get Config Parameters
 *
 * @param string $params
 * @return mixed
 */
if (!function_exists('config')) {
    function config($params)
    {
        return Config::get($params);
    }
}

/**
 * Get URL of the Named Route
 *
 * @param string $name
 * @param array $params
 * @return string
 */
if (!function_exists('route')) {
    function route($name, $params = [])
    {
        return link_to(System\Libs\Router\Router::getUrl($name, $params));
    }
}

/**
 * URL Slug Generator
 *
 * @param string $str
 * @param array $options
 * @return string
 */
if (!function_exists('slug')) {
    function slug($str, $options = array()) {
        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());

        $defaults = array(
            'delimiter' => '-',
            'limit' => null,
            'lowercase' => true,
            'replacements' => array(),
            'transliterate' => true,
        );

        // Merge options
        $options = array_merge($defaults, $options);

        $char_map = array(
            // Latin
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
            'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
            'ß' => 'ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
            'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
            'ÿ' => 'y',

            // Latin symbols
            '©' => '(c)',

            // Greek
            'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
            'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
            'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
            'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
            'Ϋ' => 'Y',
            'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
            'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
            'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
            'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
            'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',

            // Turkish
            'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
            'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',

            // Russian
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
            'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
            'Я' => 'Ya',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya',

            // Ukrainian
            'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
            'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',

            // Czech
            'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
            'Ž' => 'Z',
            'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
            'ž' => 'z',

            // Polish
            'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
            'Ż' => 'Z',
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
            'ż' => 'z',

            // Latvian
            'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
            'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
            'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
            'š' => 's', 'ū' => 'u', 'ž' => 'z'
        );

        // Make custom replacements
        $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

        // Transliterate characters to ASCII
        if ($options['transliterate']) {
            $str = str_replace(array_keys($char_map), $char_map, $str);
        }

        // Replace non-alphanumeric characters with our delimiter
        $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

        // Remove duplicate delimiters
        $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

        // Truncate slug to max. characters
        $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

        // Remove delimiter from ends
        $str = trim($str, $options['delimiter']);

        return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
    }
}
