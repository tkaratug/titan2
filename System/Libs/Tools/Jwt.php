<?php
namespace System\Libs\Tools;

class Jwt
{
    private $exp = 60;

    private $algoritms = [
        'HS256' => 'sha256'
    ];

    /**
     * Create JWT
     * 
     * @param array $payload
     * @param string $secret
     * @param string $alg
     * @return string
     */
    public function encode($payload, $secret, $alg = 'HS256')
    {
        $header = $this->urlSafeBase64Encode(json_encode([
            'typ'   => 'JWT',
            'alg'   => $alg
        ]));

        $payload['exp'] = time() + $this->exp;
        $payload['jti'] = uniqid(time());
        $payload['iat'] = time();
        $payload        = $this->urlSafeBase64Encode(json_encode($payload));

        $message    = $header . '.' . $payload;

        $signature  = $this->urlSafeBase64Encode($this->signature($message, $secret, $alg));

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
        return hash_hmac($this->algoritms[$alg], $message, $secret, true);
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
}