<?php
/*************************************************
 * Titan-2 Mini Framework
 * Form Validation Library
 *
 * Author   : Turan Karatuğ
 * Web      : http://www.titanphp.com
 * Docs     : http://kilavuz.titanphp.com 
 * Github   : http://github.com/tkaratug/titan2
 * License  : MIT   
 *
 *************************************************/
namespace System\Libs\Validation;

class Validation
{
	// Validation errors
	protected $errors 	= [];

	// Label for fields
	protected $labels	= [];

	// Validation rules
	protected $rules 	= [];

	// Data to validate
	protected $data		= [];

	/**
	 * Define Validation Rules
	 *
	 * @param array $rules
	 * @return void
	 */
	public function rules($rules)
	{
		foreach ($rules as $key => $value) {	
			$this->labels[$value['field']] 	= $value['label'];
			$this->rules[$value['field']]	= $value['rules'];
		}
	}

	/**
	 * Define One Validation Rule
	 *
	 * @param string $field
	 * @param string $label
	 * @param string $rules
	 * @return void
	 */
	public function rule($field, $label, $rules)
	{
		$this->labels[$field] 	= $label;
		$this->rules[$field]	= $rules;
	}

	/**
	 * Define Bulk Data to Validate
	 *
	 * @param array $data
	 * @return void
	 */
	public function bulkData($data)
	{
		foreach ($data as $key => $value) {
			$this->data[$value['field']] = $value['data'];
		}
	}

	/**
	 * Define Data to Validate
	 *
	 * @param string $field
	 * @param string $data
	 * @return void
	 */
	public function data($field, $data)
	{
		$this->data[$field] = $data;
	}

	/**
	 * Validate
	 *
	 * @return boolean
	 */
	public function isValid()
	{
		foreach ($this->rules as $key => $value) {
			
			$rules = explode('|', $value);

			foreach ($rules as $rule) {
				if (strpos($rule, ',')) {
					$group  = explode(',', $rule);
					$filter = $group[0];
                    $params = $group[1];

                    if ($filter == 'matches') {
                    	if ($this->matches($this->data[$key], $this->data[$params]) === false)
                    		$this->errors[] = lang('validation', $filter . '_error', ['%s' => $this->labels[$key], '%t' => $params]);
                    } else {
                    	if ($this->$filter($this->data[$key], $params) === false)
                            $this->errors[] = lang('validation', $filter . '_error', ['%s' => $this->labels[$key], '%t' => $params]);
                    }
				} else {
					if ($this->$rule($this->data[$key]) === false)
                        $this->errors[] = lang('validation', $rule . '_error', $this->labels[$key]);
				}
			}

		}

		if (count($this->errors) > 0)
            return false;
        else
            return true;
	}

	/**
     * Sanitizing Data
     *
     * @param string $data
     * @return string
     */
    public function sanitize($data)
    {
        if (!is_array($data)) {
            return filter_var(trim($data), FILTER_SANITIZE_STRING);
        } else {
            foreach ($data as $key => $value) {
                $data[$key] = filter_var($value, FILTER_SANITIZE_STRING);
            }
            return $data;
        }
    }

    /**
     * Return errors
     *
     * @return array
     */
    public function errors()
    {
    	return $this->errors;
    }

    /**
     * Required Field Control
     *
     * @param string $data
     * @return boolean
     */
    protected function required($data)
    {
        if (!empty($data) && !is_null($data) && $data !== '') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Numeric Field Control
     *
     * @param int $data
     * @return boolean
     */
    protected function numeric($data)
    {
        if (is_int($data) || is_numeric($data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Email Validation
     *
     * @param string $email
     * @return boolean
     */
    protected function email($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Minimum Character Check
     *
     * @param string $data
     * @param int $length
     * @return boolean
     */
    protected function min_len($data, $length)
    {
        if (strlen(trim($data)) < $length) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Maximum Character Check
     *
     * @param string $data
     * @param int $length
     * @return boolean
     */
    protected function max_len($data, $length)
    {
        if (strlen(trim($data)) > $length) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Exact Length Check
     *
     * @param string $data
     * @param int $length
     * @return boolean
     */
    protected function exact_len($data, $length)
    {
        if (strlen(trim($data)) == $length) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Alpha Character Validation
     *
     * @param string $data
     * @return boolean
     */
    protected function alpha($data)
    {
        if (!is_string($data)) {
            return false;
        }

        return ctype_alpha($data);
    }

    /**
     * Alphanumeric Character Validation
     *
     * @param string $data
     * @return boolean
     */
    protected function alpha_num($data)
    {
        return ctype_alnum($data);
    }

    /**
     * Alpha-dash Character Validation
     *
     * @param string $data
     * @return boolean
     */
    protected function alpha_dash($data)
    {
        return (!preg_match("/^([-a-z0-9_-])+$/i", $data)) ? false : true;
    }

    /**
     * Alpha-space Character Validation
     *
     * @param string $data
     * @return boolean
     */
    protected function alpha_space($data)
    {
        return (!preg_match("/^([A-Za-z0-9- ])+$/i", $data)) ? false : true;
    }

    /**
     * Integer Validation
     *
     * @param int $data
     * @return boolean
     */
    protected function integer($data)
    {
        if (is_int($data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Boolean Validation
     *
     * @param string $data
     * @return boolean
     */
    protected function boolean($data)
    {
        if ($data === true || $data === false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Float Validation
     *
     * @param string $data
     * @return boolean
     */
    protected function float($data)
    {
        if (is_float($data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * URL Validation
     *
     * @param string $url
     * @return boolean
     */
    protected function valid_url($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * IP Validation
     *
     * @param string $ip
     * @return boolean
     */
    protected function valid_ip($ip)
    {
        if(filter_var($ip, FILTER_VALIDATE_IP)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * IPv4 Validation
     *
     * @param string $ip
     * @return boolean
     */
    protected function valid_ipv4($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * IPv6 Validation
     *
     * @param string $ip
     * @return boolean
     */
    protected function valid_ipv6($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Credit Card Validation
     *
     * @param string $data
     * @return boolean
     */
    protected function valid_cc($data)
    {
        $number = preg_replace('/\D/', '', $data);

        if (function_exists('mb_strlen')) {
            $number_length = mb_strlen($number);
        } else {
            $number_length = strlen($number);
        }

        $parity = $number_length % 2;

        $total=0;

        for ($i=0; $i<$number_length; $i++) {
            $digit = $number[$i];

            if ($i % 2 == $parity) {
                $digit *= 2;

                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $total += $digit;
        }

        return ($total % 10 == 0) ? true : false;
    }

    /**
     * Field must contain something
     *
     * @param string $data
     * @param string $part
     * @return boolean
     */
    protected function contains($data, $part)
    {
        if (strpos($data, $part) !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Minimum Value Validation
     *
     * @param int $data
     * @param int $min
     * @return boolean
     */
    protected function min_numeric($data, $min)
    {
        if (is_numeric($data) && is_numeric($min) && $data >= $min) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Maximum Value Validation
     *
     * @param int $data
     * @param int $max
     * @return boolean
     */
    protected function max_numeric($data, $max)
    {
        if (is_numeric($data) && is_numeric($max) && $data <= $max) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Matched Fields Validation
     *
     * @param string $data
     * @param string $field
     * @return bool
     */
    protected function matches($data, $field)
    {
        if ($data == $field) {
            return true;
        } else {
            return false;
        }
    }
	
}