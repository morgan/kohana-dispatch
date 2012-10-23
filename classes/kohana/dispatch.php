<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Dispatch
 * 
 * @package		Dispatch
 * @category	Base
 * @author		Micheal Morgan <micheal@morgan.ly>
 * @copyright	(c) 2011 Micheal Morgan
 * @license		MIT
 */
class Kohana_Dispatch
{
	/**
	 * Factory pattern
	 * 
	 * @static
	 * @access	public
	 * @param	string	Path of resource
	 * @param	string	Config key
	 * @return	Dispatch_Request
	 */
	public static function factory($path = NULL, Dispatch_Connection $connection = NULL)
	{
		$request = Dispatch_Request::factory();
		
		if ($path)
		{
			$request->path($path);
		}
		
		if ($connection)
		{
			$request->connection($connection);
		}
		
		return $request;
	}
}