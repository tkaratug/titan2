<?php
/*************************************************
 * Titan-2 Mini Framework
 * Restful Client Library
 *
 * Author   : Turan Karatuğ
 * Web      : http://www.titanphp.com
 * Docs     : http://kilavuz.titanphp.com
 * Github   : http://github.com/tkaratug/titan2
 * License  : MIT
 *
 *************************************************/
namespace System\Libs\Http;

use Curl;
use System\Libs\Exception\ExceptionHandler;

class Restful
{

    /**
     * Makes get request
     *
     * @param string $url
     * @param array $data
     * @return void
     */
    public function get($url, $data = [])
    {
        if (is_null($url))
            throw new ExceptionHandler("Parametre hatası", "URL bilgisi gerekli.");

        Curl::get($url, $data);
    }

    /**
     * Makes post request
     *
     * @param string $url
     * @param array $data
     * @return void
     */
    public function post($url, $data = [])
    {
        if (is_null($url))
            throw new ExceptionHandler("Parametre hatası", "URL bilgisi gerekli.");

        Curl::post($url, $data);
    }

    /**
     * Makes put request
     *
     * @param string $url
     * @param array $data
     * @return void
     */
    public function put($url, $data = [])
    {
        if (is_null($url))
            throw new ExceptionHandler("Parametre hatası", "URL bilgisi gerekli.");

        Curl::put($url, $data);
    }

    /**
     * Makes delete request
     *
     * @param string $url
     * @param array $data
     * @return void
     */
    public function delete($url, $data = [])
    {
        if (is_null($url))
            throw new ExceptionHandler("Parametre hatası", "URL bilgisi gerekli.");

        Curl::delete($url, $data);
    }

    /**
     * Define Request Header
     *
     * @param array|string $header
     * @return array|string
     */
    public function setHeader($header, $value = null)
    {
        if (is_null($header))
            throw new ExceptionHandler("Parametre hatası", "Header bilgisi gerekli.");

        Curl::setHeader($header, $value);

        return $this;
    }

    /**
     * Returns response
     *
     * @return string
     */
    public function response()
    {
        return Curl::responseBody();
    }

    /**
     * Returns status code
     *
     * @return integer
     */
    public function statusCode()
    {
        return Curl::responseHeader('Status-Code');
    }

}
