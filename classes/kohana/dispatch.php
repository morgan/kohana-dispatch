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
	public function factory($path, $connection = NULL, array $config = array())
	{
		return Dispatch_Request::factory($connection, $config)->path($path);
	}
}