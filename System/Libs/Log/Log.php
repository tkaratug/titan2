<?php
/*************************************************
 * Titan-2 Mini Framework
 * Log Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT
 *
 *************************************************/
namespace System\Libs\Log;

use System\Libs\Exception\ExceptionHandler;

class Log
{
	/**
	 * Save log as emergency
	 *
	 * @param string $message
	 * @throws \Exception
	 */
	public function emergency($message)
	{
		$this->write('emergency', $message);
	}

	/**
	 * Save log as alert
	 *
	 * @param string $message
	 * @throws \Exception
	 */
	public function alert($message)
	{
		$this->write('alert', $message);
	}

	/**
	 * Save log as critical
	 *
	 * @param string $message
	 * @throws \Exception
	 */
	public function critical($message)
	{
		$this->write('critical', $message);
	}

	/**
	 * Save log as error
	 *
	 * @param string $message
	 * @throws \Exception
	 */
	public function error($message)
	{
		$this->write('error', $message);
	}

	/**
	 * Save log as warning
	 *
	 * @param string $message
	 * @throws \Exception
	 */
	public function warning($message)
	{
		$this->write('warning', $message);
	}

	/**
	 * Save log as notice
	 *
	 * @param string $message
	 * @throws \Exception
	 */
	public function notice($message)
	{
		$this->write('notice', $message);
	}

	/**
	 * Save log as info
	 *
	 * @param string $message
	 * @throws \Exception
	 */
	public function info($message)
	{
		$this->write('info', $message);
	}

	/**
	 * Save log as debug
	 *
	 * @param string $message
	 * @throws \Exception
	 */
	public function debug($message)
	{
		$this->write('debug', $message);
	}

    /**
     * Write logs to file
     *
     * @param string $level
     * @param string $message
     * @throws \Exception
     */
	protected function write($level, $message)
	{
		if (is_array($message))
			$message = serialize($message);

		$logText = '[' . date('Y-m-d H:i:s') . '] - [' . $level . '] ---> ' . $message;
		$this->save($logText);
	}

    /**
     * Save Log
     *
     * @param string $logText
     * @throws \Exception
     */
	protected function save($logText)
	{
		$fileName 	= 'Log_' . date('Y-m-d') . '.log';
		$file 		= fopen(APP_DIR . 'Storage/Logs/' . $fileName, 'a');

		if (fwrite($file, $logText . "\n") === false)
			throw new ExceptionHandler("Hata", "Log dosyası oluşturulamadı. Yazma izinlerini kontrol ediniz.");

		fclose($file);
	}

}
