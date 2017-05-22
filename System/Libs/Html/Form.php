<?php
/*************************************************
 * Titan-2 Mini Framework
 * Form Builder Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
namespace System\Libs\Html;

class Form
{

	/**
	 * Form open
	 *
	 * @param array $attr
	 * @return string
	 */
	public function open($attr = [])
	{

		if (!is_array($attr))
			return null;

		if (!empty($attr)) {

			$form = '<form ';

			foreach ($attr as $key => $val) {
				$form .= $key . '="' . $val . '" ';
			}

			$form = trim($form);
			$form .= '>';

		} else {

			$form = '<form>';

		}

		return $form . "\n";
	}

	/**
	 * Form close
	 *
	 * @return string
	 */
	public function close()
	{
		return "</form>\n";
	}

	/**
	 * Form label
	 *
	 * @param string $for
	 * @param string $text
	 * @return string
	 */
	public function label($for, $text)
	{
		return '<label for="' . $for . '">' . $text . '</label>';
	}

	/**
	 * Text element
	 *
	 * @param string $name
	 * @param array $attr
	 * @return string
	 */
	public function text($name, $attr = [])
	{
		$input = '<input type="text" name="' . $name . '" ';

		if (!empty($attr)) {
			if (!array_key_exists('id', $attr))
				$input .= 'id="' . $name . '" ';

			foreach($attr as $key => $val) {
				$input .= $key . '="' . $val . '" ';
			}
		}

		$input = trim($input);
		$input .= '>';

		return $input . "\n";
	}

	/**
	 * Password element
	 *
	 * @param string $name
	 * @param array $attr
	 * @return string
	 */
	public function password($name, $attr = [])
	{
		$input = '<input type="password" name="' . $name . '" ';

		if (!empty($attr)) {
			if (!array_key_exists('id', $attr))
				$input .= 'id="' . $name . '" ';

			foreach($attr as $key => $val) {
				$input .= $key . '="' . $val . '" ';
			}
		}

		$input = trim($input);
		$input .= '>';

		return $input . "\n";
	}

	/**
	 * Email element
	 *
	 * @param string $name
	 * @param array $attr
	 * @return string
	 */
	public function email($name, $attr = [])
	{
		$input = '<input type="email" name="' . $name . '" ';

		if (!empty($attr)) {
			if (!array_key_exists('id', $attr))
				$input .= 'id="' . $name . '" ';

			foreach($attr as $key => $val) {
				$input .= $key . '="' . $val . '" ';
			}
		}

		$input = trim($input);
		$input .= '>';

		return $input . "\n";
	}

	/**
	 * Hidden element
	 *
	 * @param string $name
	 * @param array $attr
	 * @return string
	 */
	public function hidden($name, $attr = [])
	{
		$input = '<input type="hidden" name="' . $name . '" ';

		if (!empty($attr)) {
			if (!array_key_exists('id', $attr))
				$input .= 'id="' . $name . '" ';

			foreach($attr as $key => $val) {
				$input .= $key . '="' . $val . '" ';
			}
		}

		$input = trim($input);
		$input .= '>';

		return $input . "\n";
	}

	/**
	 * Textarea element
	 *
	 * @param string $name
	 * @param array $attr
	 * @return string
	 */
	public function textarea($name, $attr = [])
	{
		$input = '<textarea name="' . $name . '" ';

		if (!empty($attr)) {
			if (!array_key_exists('id', $attr))
				$input .= 'id="' . $name . '" ';

			foreach($attr as $key => $val) {
				if ($key != 'content')
					$input .= $key . '="' . $val . '" ';
			}
		}

		$input = trim($input);
		$input .= '>';

		if (array_key_exists('content', $attr))
			$input .= $attr['content'];

		$input .= '</textarea>';

		return $input . "\n";
	}

	/**
	 * Select element
	 *
	 * @param string $name
	 * @param array $options
	 * @param string $selected
	 * @param array $attr
	 * @return string
	 */
	public function select($name, $options = [], $selected = null, $attr = [])
	{
		$input = '<select name="' . $name . '" ';

		if (!empty($attr)) {
			if (!array_key_exists('id', $attr))
				$input .= 'id="' . $name . '" ';

			foreach($attr as $key => $val) {
				$input .= $key . '="' . $val . '" ';
			}
		}

		$input = trim($input);
		$input .= '>';

		$dropdown = '';
		if (!empty($options)) {
			foreach ($options as $key => $val) {
				if (!is_null($selected) && $selected == $key)
					$dropdown .= '<option value="' . $key . '" selected>' . $val . '</option>';
				else
					$dropdown .= '<option value="' . $key . '">' . $val . '</option>';
			}
		}

		return $input . "\n" . $dropdown . "\n" . '</select>' . "\n";
	}

	/**
	 * Multiple Select element
	 *
	 * @param string $name
	 * @param array $options
	 * @param array $selected
	 * @param array $attr
	 * @return string
	 */
	public function multiSelect($name, $options = [], $selected = [], $attr = [])
	{
		$input = '<select name="' . $name . '" ';

		if (!empty($attr)) {
			if (!array_key_exists('id', $attr))
				$input .= 'id="' . $name . '" ';

			foreach($attr as $key => $val) {
				$input .= $key . '="' . $val . '" ';
			}
		}

		$input .= 'multiple="multiple">';

		$dropdown = '';
		if (!empty($options)) {
			foreach ($options as $key => $val) {
				if (!empty($selected)) {
					if (in_array($key, $selected))
						$dropdown .= '<option value="' . $key . '" selected>' . $val . '</option>';
					else
						$dropdown .= '<option value="' . $key . '">' . $val . '</option>';
				} else {
					$dropdown .= '<option value="' . $key . '">' . $val . '</option>';
				}
			}
		}

		return $input . "\n" . $dropdown . "\n" . '</select>' . "\n";
	}

	/**
	 * Checkbox element
	 *
	 * @param string $name
	 * @param int|string $value
	 * @param boolean $checked
	 * @param array $attr
	 * @return string
	 */
	public function checkbox($name, $value = '', $checked = false, $attr = [])
	{
		$input = '<input type="checkbox" name="' . $name . '" ';

		if (!empty($attr)) {
			if (!array_key_exists('id', $attr))
				$input .= 'id="' . $name . '" ';

			foreach($attr as $key => $val) {
				$input .= $key . '="' . $val . '" ';
			}
		}

		$input .= 'value="' . $value . '" ';

		if($checked)
			$input .= 'checked';

		$input = trim($input);
        $input .= '>';

        return $input . "\n";
	}

	/**
	 * Radio element
	 *
	 * @param string $name
	 * @param int|string $value
	 * @param boolean $checked
	 * @param array $attr
	 * @return string
	 */
	public function radio($name, $value = '', $checked = false, $attr = [])
	{
		$input = '<input type="radio" name="' . $name . '" ';

		if (!empty($attr)) {
			if (!array_key_exists('id', $attr))
				$input .= 'id="' . $name . '" ';

			foreach($attr as $key => $val) {
				$input .= $key . '="' . $val . '" ';
			}
		}

		$input .= 'value="' . $value . '" ';

		if($checked)
			$input .= 'checked';

		$input = trim($input);
        $input .= '>';

        return $input . "\n";
	}

	/**
	 * File element
	 *
	 * @param string $name
	 * @param boolean $multiple
	 * @param array $attr
	 * @return string
	 */
	public function file($name, $multiple = false, $attr = [])
	{
		$input = '<input type="file" ';

		if($multiple)
			$input .= 'name="' . $name . '[]" multiple="multiple" ';
		else
			$input .= 'name="' . $name . '" ';

		if (!empty($attr)) {
			if (!array_key_exists('id', $attr))
				$input .= 'id="' . $name . '" ';

			foreach($attr as $key => $val) {
				$input .= $key . '="' . $val . '" ';
			}
		}

		$input = trim($input);
        $input .= '>';

        return $input . "\n";
	}

	/**
	 * Submit element
	 *
	 * @param string $name
	 * @param string $value
	 * @param array $attr
	 * @return string
	 */
	public function submit($name, $value = '', $attr = [])
	{
		$input = '<input type="submit" name="' . $name . '" ';

		if (!empty($attr)) {
			if (!array_key_exists('id', $attr))
				$input .= 'id="' . $name . '" ';

			foreach($attr as $key => $val) {
				$input .= $key . '="' . $val . '" ';
			}
		}

		$input .= 'value="' . $value . '" ';

		$input = trim($input);
		$input .= '>';

		return $input . "\n";
	}

	/**
	 * Button element
	 *
	 * @param string $name
	 * @param string $value
	 * @param array $attr
	 * @return string
	 */
	public function button($name, $value = '', $attr = [])
	{
		$input = '<button type="button" name="' . $name . '" ';

		if (!empty($attr)) {
			if (!array_key_exists('id', $attr))
				$input .= 'id="' . $name . '" ';

			foreach($attr as $key => $val) {
				$input .= $key . '="' . $val . '" ';
			}
		}

		$input = trim($input);
		$input .= '>';
		$input .= $value;
		$input .= '</button>';

		return $input . "\n";
	}

}