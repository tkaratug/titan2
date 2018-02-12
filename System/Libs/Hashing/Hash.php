<?php
namespace System\Libs\Hashing;

use System\Libs\Exception\ExceptionHandler;

class Hash
{
    /**
     * Default crypt cost factor
     *
     * @var int
     */
    protected $cost = 10;

    /**
     * Hash the given value
     *
     * @param string $value
     * @param array $options
     * @return string
     */
    public function make($value, array $options = [])
    {
        if (!array_key_exists('cost', $options))
            $options['cost'] = $this->cost;

        $hash = password_hash($value, PASSWORD_DEFAULT, $options);

        if ($hash === false)
            throw new ExceptionHandler('Hata', 'Bcrypt hash desteklenmiyor');

        return $hash;
    }

    /**
     * Check the given value against a hash
     *
     * @param string $value
     * @param string $hashedValue
     * @return bool
     */
    public function check($value, $hashedValue)
    {
        return password_verify($value, $hashedValue);
    }

    /**
     * Check if the given hash has been hashed using the given options
     *
     * @param string $hashedValue
     * @param array $options
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = [])
    {
        if (!array_key_exists('cost', $options))
            $options['cost'] = $this->cost;

        return password_needs_rehash($hashedValue, PASSWORD_DEFAULT, $options);
    }
}
