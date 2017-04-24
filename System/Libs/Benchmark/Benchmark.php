<?php
/*************************************************
 * Titan-2 Mini Framework
 * Benchmark Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
namespace System\Libs\Benchmark;

class Benchmark
{
	// Start microtime
	protected $startTime;

	// End microtime
	protected $endTime;

	// Memory usage
	protected $memoryUsage;

	// Memory peak
	protected $memoryPeak;

	/**
	 * Set start microtime
	 *
	 * @return void
	 */
	public function start()
	{
		$this->startTime 	= microtime(true);
	}

	/**
	 * Set end microtime
	 *
	 * @return void
	 */
	public function end()
	{
		$this->endTime 		= microtime(true);
		$this->memoryUsage 	= memory_get_usage();
	}

	/**
	 * Get the elapsed time, readable or not
	 *
	 * @param boolean $raw
	 * @param string $format
	 * @return string|float
	 */
	public function getTime($raw = false, $format = null)
	{
		$elapsedTime = $this->endTime - $this->startTime;

		return $raw ? $elapsedTime : $this->readableElapsedTime($elapsedTime, $format);
	}

	/**
	 * Get the memory usage at the end checkpoint
	 *
	 * @param boolean $raw
	 * @param string $format
	 * @return string|float
	 */
	public function getMemoryUsage($raw = false, $format = null)
	{
		return $raw ? $this->memoryUsage : $this->readableSize($this->memoryUsage, $format);
	}

	/**
	 * Get the memory peak, readable or not
	 *
	 * @param boolean $raw
	 * @param string $format
	 * @return string|float
	 */
	public function getMemoryPeak($raw = false, $format = null)
	{
		$this->memoryPeak = memory_get_peak_usage();

		return $raw ? $this->memoryPeak : $this->readableSize($this->memoryPeak, $format);
	}

	/**
	 * Wraps a callable with start() and end() calls
	 *
	 * @param callable $callable
	 * @return mixed
	 */
	public function run(callable $callable)
	{
		$arguments = func_get_args();
		array_shift($arguments);

		$this->start();
		$result = call_user_func_array($callable, $arguments);
		$this->end();

		return $result;
	}

	/**
	 * Get a human readable memory size
	 *
	 * @param int $size
	 * @param string $format
	 * @param int $round
	 * @return string
	 */
	public function readableSize($size, $format = null, $round = 3)
	{
		$mod = 1024;

		if (is_null($format)) {
			$format = '%.2f%s';
		}

		$units = explode(' ','B Kb Mb Gb Tb');

		for ($i = 0; $size > $mod; $i++) {
			$size /= $mod;
        }

        if (0 === $i) {
        	$format = preg_replace('/(%.[\d]+f)/', '%d', $format);
        }

        return sprintf($format, round($size, $round), $units[$i]);
	}

	/**
	 * Get a human readable elapsed time
	 *
	 * @param float $microtime
	 * @param string $format
	 * @param int round
	 * @return string
	 */
	public function readableElapsedTime($microtime, $format = null, $round = 3)
	{
		if (is_null($format)) {
			$format = '%.3f%s';
		}

		if ($microtime >= 1) {
			$unit = 's';
			$time = round($microtime, $round);
		} else {
			$unit = 'ms';
			$time = round($microtime*1000);
		}

		$format = preg_replace('/(%.[\d]+f)/', '%d', $format);		

		return sprintf($format, $time, $unit);
	}

}