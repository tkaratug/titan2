<?php
/*************************************************
 * Titan-2 Mini Framework
 * Facade Library
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/
namespace System\Facades;

abstract class Facade
{
	// Application List in Service Provider array
	protected static $applications;

	// Resolved instances of objects in Facade array
	protected static $reselovedInstance;

	// Created instances of objects in Facade array
	protected static $createdInstances = [];

	/**
	 * Resolved Instance
	 *
	 * @param string $facadeName
	 * @return string
	 */
	protected static function resolveInstance($facadeName)
	{
		if (is_object($facadeName)) {
			return $facadeName;
		}

		if (isset(static::$reselovedInstance[$facadeName])) {
			return static::$reselovedInstance[$facadeName];
		}

		return static::$reselovedInstance[$facadeName] = static::$applications['providers'][$facadeName];
	}

	/**
	 * Set Facade Application
	 *
	 * @param string $app
	 * @return void
	 */
	public static function setFacadeApplication($app)
	{
		static::$applications = $app;
	}

	/**
	 * Clear Resolved Instance
	 *
	 * @param string $facadeName
	 * @return void
	 */
	public static function clearResolvedInstance($facadeName)
	{
		unset(static::$reselovedInstance[$facadeName]);
	}

	/**
	 * Clear All Resolved Instances
	 *
	 * @return void
	 */
	public static function clearResolvedInstances()
	{
		static::$reselovedInstance = [];
	}

	/**
	 * Call Methods in Application Object
	 *
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public static function __callStatic($method, $args)
	{
		// Get Facade Accessor
		$accessor 	= static::getFacadeAccessor();

		// Get Service Provider
		$provider 	= static::resolveInstance($accessor);

		// Get Instance of Service Provider If it doesn't exist
		if (!array_key_exists($accessor, static::$createdInstances)) {
			static::$createdInstances[$accessor] = new $provider;
		}
		
		return call_user_func_array([static::$createdInstances[$accessor], $method], $args);
	}
	
}