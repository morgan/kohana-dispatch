<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Refactor to Response to allow for body to pass-through without being casted to a string. This 
 * allows for setting and getting objects (that are to be casted to strings) for use in sub requests.
 * 
 * Response wrapper. Created as the result of any [Request] execution
 * or utility method (i.e. Redirect). Implements standard HTTP
 * response format.
 *
 * @package    Kohana
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2008-2011 Kohana Team
 * @license    http://kohanaphp.com/license
 * @since      3.1.0
 */
class Dispatch_Kohana_Response extends Kohana_Response
{
	/**
	 * Get raw body
	 * 
	 * Seperate call to keep Response::body consistant with unit tests.
	 * 
	 * @access	public
	 * @return	mixed
	 */
	public function get_body()
	{
		return $this->_body;
	}
	
	/**
	 * Outputs the body when cast to string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->_body;
	}	
	
	/**
	 * Gets or sets the body of the response
	 *
	 * @return  mixed
	 */
	public function body($content = NULL)
	{
		if ($content === NULL)
			return (string) $this->_body;

		$this->_body = $content;
		
		return $this;
	}

	/**
	 * Returns the length of the body for use with
	 * content header
	 *
	 * @return  integer
	 */
	public function content_length()
	{
		return strlen((string) $this->body());
	}
}