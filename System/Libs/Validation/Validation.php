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
     * @param array $params
	 * @return void
	 */
	public function rules($rules, $params = [])
	{
		foreach ($rules as $key => $value) {
			$this->labels[$key] = $value['label'];
            $this->rules[$key]	= $value['rules'];
            
            if (!empty($params))
                $this->data[$key] = $params[$key];
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
			$this->data[$key] = $value;
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

			if (in_array('nullable', $rules)) {
			    $nullableFieldKey = array_search('nullable', $rules);
			    unset($rules[$nullableFieldKey]);

			    $nullable = true;
            } else {
			    $nullable = false;
            }

			foreach ($rules as $rule) {
				if (strpos($rule, ',')) {
					$group  = explode(',', $rule);
					$filter = $group[0];
                    $params = $group[1];

                    if ($filter == 'matches') {
                    	if ($this->matches($this->data[$key], $this->data[$params]) === false)
                    		$this->errors[$key] = lang('validation', $filter . '_error', ['%s' => $this->labels[$key], '%t' => $this->labels[$params]]);
                    } else {
                        if ($nullable === true) {
                            if ($this->nullable($this->data[$key]) === false && $this->$filter($this->data[$key], $params) === false)
                                $this->errors[$key] = lang('validation', $filter . '_error', ['%s' => $this->labels[$key], '%t' => $params]);
                        } else {
                            if ($this->$filter($this->data[$key], $params) === false)
                                $this->errors[$key] = lang('validation', $filter . '_error', ['%s' => $this->labels[$key], '%t' => $params]);
                        }
                    }
				} else {
				    if ($nullable === true) {
				        if ($this->nullable($this->data[$key]) === false && $this->$rule($this->data[$key]) === false)
				            $this->errors[$key] = lang('validation', $rule . '_error', $this->labels[$key]);
                    } else {
                        if ($this->$rule($this->data[$key]) === false)
                            $this->errors[$key] = lang('validation', $rule . '_error', $this->labels[$key]);
                    }
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
     * Nullable Field Control
     *
     * @param string $data
     * @return boolean
     */
    protected function nullable($data)
    {
        return is_array($data) ? (empty($data) === true) : (trim($data) === '');
        /*
        if (empty($data) || is_null($data) || $data == '') {
            return true;
        } else {
            return false;
        }
        */
    }

    /**
     * Required Field Control
     *
     * @param string $data
     * @return boolean
     */
    protected function required($data)
    {
        return is_array($data) ? (empty($data) === false) : (trim($data) !== '');
    }

    /**
     * Numeric Field Control
     *
     * @param int $data
     * @return boolean
     */
    protected function numeric($data)
    {
        return is_numeric($data);
    }

    /**
     * Email Validation
     *
     * @param string $email
     * @return boolean
     */
    protected function email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
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
        return (strlen(trim($data)) < $length) === false;
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
        return (strlen(trim($data)) > $length) === false;
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
        return (strlen(trim($data)) == $length) !== false;
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
        return filter_var($data, FILTER_VALIDATE_INT);
    }

    /**
     * Boolean Validation
     *
     * @param string $data
     * @return boolean
     */
    protected function boolean($data)
    {
        $acceptable = [true, false, 0, 1, '0', '1'];

        return in_array($data, $acceptable, true);
    }

    /**
     * Float Validation
     *
     * @param string $data
     * @return boolean
     */
    protected function float($data)
    {
        return filter_var($data, FILTER_VALIDATE_FLOAT);
    }

    /**
     * URL Validation
     *
     * @param string $url
     * @return boolean
     */
    protected function valid_url($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * IP Validation
     *
     * @param string $ip
     * @return boolean
     */
    protected function valid_ip($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP);
    }

    /**
     * IPv4 Validation
     *
     * @param string $ip
     * @return boolean
     */
    protected function valid_ipv4($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    /**
     * IPv6 Validation
     *
     * @param string $ip
     * @return boolean
     */
    protected function valid_ipv6($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
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
        return strpos($data, $part) !== false;
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
        return (is_numeric($data) && is_numeric($min) && $data >= $min) !== false;
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
        return (is_numeric($data) && is_numeric($max) && $data <= $max) !== false;
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
        return ($data == $field) !== false;
    }

}
