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
    protected $round = 10;

    /**
     * Hash the given value
     *
     * @param string $value
     * @param array $options
     * @return string
     */
    public function make($value, array $options = [])
    {
        $cost = isset($options['rounds']) ? $options[$rounds] : $this->round;

        $hash = password_hash($value, PASSWORD_DEFAULT, ['cost' => $cost]);

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
        $cost = isset($options['rounds']) ? $options['rounds'] : $this->round;

        return password_needs_rehash($hashedValue, PASSWORD_DEFAULT, ['cost' => $cost]);
    }
}
