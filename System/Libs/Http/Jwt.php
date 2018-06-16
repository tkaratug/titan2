<?php
namespace System\Libs\Http;

use System\Libs\Exception\ExceptionHandler;

class Jwt
{
    private $exp        = 60;

    private $leeway     = 0;

    private $algorithms = [
        'HS256' => 'SHA256',
        'HS512' => 'SHA512',
        'HS384' => 'SHA384'
    ];

    /**
     * Create JWT
     * 
     * @param array $payload
     * @param string $secret
     * @param string $alg
     * @return string
     */
    public function encode($payload, $secret, $alg = 'HS256', $head = null)
    {
        $header = [
            'typ'   => 'JWT',
            'alg'   => $alg
        ];

        if ($head !== null && is_array($head)) {
            array_merge($head, $header);
        }

        $payload['exp'] = time() + $this->exp;
        $payload['jti'] = uniqid(time());
        $payload['iat'] = time();

        $header         = $this->urlSafeBase64Encode($this->jsonEncode($header));
        $payload        = $this->urlSafeBase64Encode($this->jsonEncode($payload));
        $message        = $header . '.' . $payload;

        $signature      = $this->urlSafeBase64Encode($this->signature($message, $secret, $alg));

        return $header . '.' . $payload . '.' . $signature;
    }

    /**
     * Decode JWT
     * 
     * @param string $token
     * @param string $secret
     * @return object
     */
    public function decode($token, $secret)
    {
        if (empty($secret))
            throw new \Exception('Secret may not be empty');

        $jwt    = explode('.', $token);

        if (count($jwt) != 3)
            throw new \Exception('Wrong number of segments');

        list ($head64, $payload64, $sign64) = $jwt;

        if (null === ($header = $this->jsonDecode($this->urlSafeBase64Decode($head64))))
            throw new \Exception('Invalid header encoding');

        if (null === ($payload = $this->jsonDecode($this->urlSafeBase64Decode($payload64))))
            throw new \Exception('Invalid claims encoding');

        if (false === ($signature = $this->urlSafeBase64Decode($sign64)))
            throw new \Exception('Invalid signature encoding');

        if (empty($header->alg))
            throw new \Exception('Empty algorithm');

        if (empty($this->algorithms[$header->alg]))
            throw new \Exception('Algorithm not supported');

        if (!$this->verify("$head64.$payload64", $signature, $secret, $header->alg))
            throw new \Exception('Signature verification failed');

        if (isset($payload->nbf) && $payload->nbf > (time() + $this->leeway))
            throw new \Exception('Cannot handle token prior to ' . date(\DateTime::ISO8601, $payload->nbf));

        if (isset($payload->iat) && $payload->iat > (time() + $this->leeway))
            throw new \Exception('Cannot handle token prior to ' . date(\DateTime::ISO8601, $payload->iat));

        if (isset($payload->exp) && (time() - $this->leeway) >= $payload->exp)
            throw new \Exception('Expired token');

        return $payload;
    }

    /**
     * Make Signature
     * 
     * @param string $message
     * @param string $secret
     * @param string $alg
     * @return string
     */
    private function signature($message, $secret, $alg)
    {
        if (!array_key_exists($alg, $this->algorithms))
            throw new \Exception('Algorithm not supported');

        return hash_hmac($this->algorithms[$alg], $message, $secret, true);
    }

    /**
     * Verify a signature with message and secret key
     * 
     * @param string $message
     * @param string $signature
     * @param string $secret
     * @param string $alg
     * @return bool
     */
    private function verify($message, $signature, $secret, $alg)
    {
        if (empty($this->algorithms[$alg]))
            throw new \Exception('Algorithm not supported');

        $hash   = hash_hmac($this->algorithms[$alg], $message, $secret, true);

        if (function_exists('hash_equals')) {
            return hash_equals($signature, $hash);
        }

        $len    = min($this->safeStrLen($signature), $this->safeStrLen($hash));
        $status = 0;

        for ($i = 0; $i < $len; $i++) {
            $status |= (ord($signature[$i]) ^ ord($hash[$i]));
        }

        $status |= ($this->safeStrLen($signature) ^ $this->safeStrLen($hash));

        return ($status === 0);
    }

    /**
     * URL Safe Base64 Encode
     * 
     * @param string $data
     * @return string
     */
    private function urlSafeBase64Encode($data)
    {
        return str_replace('=', '', strtr(base64_encode($data), '+/', '-_'));
    }

    /**
     * URL Safe Base64 Decode
     * 
     * @param string $data
     * @return string
     */
    private function urlSafeBase64Decode($data)
    {
        $remainder  = strlen($data) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $data  .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
    
    /**
     * Encode a PHP object|array into a JSON string
     * 
     * @param object|array $data
     * @return string
     */
    private function jsonEncode($data)
    {
        $json = json_encode($data);
        
        if (function_exists('json_last_error') && $errno = json_last_error()) {
            $this->handleJsonError($errno);
        } elseif ($json === 'null' && $data !== null) {
            throw new \Exception('Null result with non-null input');
        }

        return $json;
    }

    /**
     * Decode a JSON string to a PHP object
     * 
     * @param string $data
     * @return object
     */
    private function jsonDecode($data)
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=') && !(defined('JSON_C_VERSION') && PHP_INT_SIZE > 4)) {
            $obj                    = json_decode($data, false, 512, JSON_BIGINT_AS_STRING);
        } else {
            $max_int_length         = strlen((string) PHP_INT_MAX) - 1;
            $json_without_bigints   = preg_replace('/:\s*(-?\d{'.$max_int_length.',})/', ': "$1"', $$data);
            $obj                    = json_decode($json_without_bigints);
        }

        if (function_exists('json_last_error') && $errno = json_last_error()) {
            static::handleJsonError($errno);
        } elseif ($obj === null && $data !== 'null') {
            throw new \Exception('Null result with non-null input');
        }

        return $obj;
    }

    /**
     * Helper to create a json error
     * 
     * @param int $errno
     * @return void
     */
    private function handleJsonError($errno)
    {
        $messages = [
            JSON_ERROR_DEPTH            => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH   => 'Invalid or malformed JSON',
            JSON_ERROR_CTRL_CHAR        => 'Unexpected control character found',
            JSON_ERROR_SYNTAX           => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8             => 'Malformed UTF-8 characters'
        ];

        throw new \Exception(
            isset($messages[$errno])
            ? $messages[$errno]
            : 'Unknown JSON error: ' . $errno
        );
    }

    /**
     * Get the number of bytes in cryptographic strings.
     * 
     * @param string $str
     * @return int
     */
    private function safeStrLen($str)
    {
        if (function_exists('mb_strlen'))
            return mb_strlen($str, '8bit');

        return strlen($str);
    }
}