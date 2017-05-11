<?php
/*************************************************
 * Titan-2 Mini Framework
 * HTML Builder Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
namespace System\Libs\Html;

class Html
{

	/**
	 * Convert an HTML string to entities
	 *
	 * @param string $value
	 * @return string
	 */
	public function entities($value)
	{
		return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
	}

	/**
	 * Convert entities to HTML characters
	 *
	 * @param string $value
	 * @return string
	 */
	public function decode($value)
	{
		return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
	}

	/**
	 * Include javascript file
	 *
	 * @param string $value
	 * @return string
	 */
	public function script($url)
	{
		return '<script type="text/javascript" src="' . $url . '"></script>';
	}

	/**
	 * Include Stylesheet file
	 *
	 * @param string $url
	 * @return string
	 */
	public function style($url)
	{
		return '<link rel="stylesheet" type="text/css" href="' . $url . '" />';
	}

	/**
	 * Create meta keywords
	 *
	 * @param string $content
	 * @return string
	 */
	public function keywords($content)
	{
		return '<meta name="keywords" content="' . $content . '" />';
	}

	/**
	 * Create meta description
	 *
	 * @param string $content
	 * @return string
	 */
	public function description($content)
	{
		return '<meta name="description" content="' . $content . '" />';
	}

	/**
	 * Create facebook open graph meta tags
	 *
	 * @param string|array $url
	 * @param string $type
	 * @param string $title
	 * @param string $descr
	 * @param string $image
	 * @return string
	*/
	public function openGraph($url, $type = null, $title = null, $descr = null, $image = null)
	{
		$response = '';
		if (is_array($url)) {
			foreach ($url as $key => $val) {
				$response .= '<meta property="og:' . $key . '" content="' . $val . '" />' . "\n\t";
			}
		} else {
			$response .= '<meta property="og:url" content="' . $url . '" />' . "\n\t";
			$response .= '<meta property="og:type" content="' . $type . '" />' . "\n\t";
			$response .= '<meta property="og:title" content="' . $title . '" />' . "\n\t";
			$response .= '<meta property="og:description" content="' . $descr . '" />' . "\n\t";
			$response .= '<meta property="og:image" content="' . $image . '" />' . "\n\t";
		}

		return $response;
	}

	/**
	 * Create twitter cards meta tags
	 *
	 * @param string|array $card
	 * @param string $url
	 * @param string $title
	 * @param string $descr
	 * @param string $image
	 * @return string
	*/
	public function twitterCards($card, $url = null, $title = null, $descr = null, $image = null)
	{
		$response = '';
		if (is_array($card)) {
			foreach ($card as $key => $val) {
				$response .= '<meta name="twitter:' . $key . '" content="' . $val . '" />' . "\n\t";
			}
		} else {
			$response .= '<meta name="twitter:card" content="' . $card . '" />' . "\n\t";
			$response .= '<meta name="twitter:url" content="' . $url . '" />' . "\n\t";
			$response .= '<meta name="twitter:title" content="' . $title . '" />' . "\n\t";
			$response .= '<meta name="twitter:description" content="' . $descr . '" />' . "\n\t";
			$response .= '<meta name="twitter:image" content="' . $image . '" />' . "\n\t";
		}

		return $response;
	}

	/**
	 * Generate a Html image element
	 *
	 * @param string $url
	 * @param string $alt
	 * @param array $attr
	 * @return string
	 */
	public function image($url, $alt = null, $attr = [])
	{
		$image = '<img src="' . $url . '" ';

		if (!is_null($alt))
			$image .= 'alt="' . $alt . '" ';

		if (!empty($attr)) {
			foreach ($attr as $key => $val) {
				$image .= $key . '="' . $val . '" ';
			}
		}

		$image = trim($image);
		$image .= '/>';

		return $image;
	}

	/**
	 * Generate a Html anchor element
	 *
	 * @param string $url
	 * @param string $title
	 * @param array $attr
	 * @return string
	 */
	public function link($url, $title, $attr = [])
	{
		$link = '<a href="' . $url . '" ';

		if (!empty($attr)) {
			foreach ($attr as $key => $val) {
				$link .= $key . '="' . $val . '" ';
			}
		}

		$link = trim($link);
		$link .= '>' . $title . '</a>';

		return $link;
	}

	/**
	 * Obfuscate an e-mail address to prevent spam-bots
	 *
	 * @param string $email
	 * @return string
	 */
	public function email($email)
	{
		$character_set 	= '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';
		$key 			= str_shuffle($character_set); 
		$cipher_text 	= ''; 
		$id 			= 'e'.rand(1,999999999);

		for ($i = 0; $i < strlen($email); $i++) {
			$cipher_text .= $key[strpos($character_set, $email[$i])];
		}

	  	$script = 'var a="'.$key.'";var b=a.split("").sort().join("");var c="'.$cipher_text.'";var d="";';
	  	$script.= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));';
	  	$script.= 'document.getElementById("'.$id.'").innerHTML="<a href=\\"mailto:"+d+"\\">"+d+"</a>"';

	  	$script = "eval(\"".str_replace(array("\\",'"'),array("\\\\",'\"'), $script)."\")";
	  	$script = '<script type="text/javascript">/*<![CDATA[*/'.$script.'/*]]>*/</script>';

	  	return '<span id="'.$id.'">[javascript protected email address]</span>'.$script;
	}

}