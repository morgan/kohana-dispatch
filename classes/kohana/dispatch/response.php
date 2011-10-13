<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Dispatch Response
 * 
 * @package		Dispatch
 * @category	Base
 * @author		Micheal Morgan <micheal@morgan.ly>
 * @copyright	(c) 2011 Micheal Morgan
 * @license		MIT
 */
class Kohana_Dispatch_Response implements Iterator, ArrayAccess, Countable
{	
	/**
	 * Response
	 * 
	 * @access	protected
	 * @var		Response|FALSE
	 */
	protected $_response = FALSE;
	
	/**
	 * Data
	 * 
	 * @access	protected
	 * @var		NULL|array
	 */
	protected $_data = array();
	
	/**
	 * Factory pattern
	 * 
	 * @access	public
	 * @return	Dispatch_Response
	 */
	public static function factory($response)
	{
		return new Dispatch_Response($response);	
	}
	
	/**
	 * Initialization
	 * 
	 * @access	public
	 * @param	Response|FALSE
	 * @return	void
	 */
	public function __construct($response)
	{
		$this->_response = $response;
		
		$this->_data = ($this->_response instanceof Response) ? $this->_filter_response($this->_response) : array();
	}
	
	/**
	 * Whether or not response is loaded
	 * 
	 * @access	public
	 * @return	bool
	 */
	public function loaded()
	{
		if ($this->_response instanceof Response)
		{
			$status = $this->_response->status();
			
			if ($status >= 200 && $status <= 300)
				return TRUE;
		}
		
		return FALSE;
	}

	/**
	 * Return Response as array
	 * 
	 * @access	public
	 * @return	array
	 */
	public function as_array()
	{
		return $this->_data;
	}
	
	/**
	 * Get response
	 * 
	 * @access	public
	 * @return	Response|FALSE
	 */
	public function get_response()
	{
		return $this->_response;
	}

	/**
	 * Filter Response
	 * 
	 * @todo	Detect empty body and return empty array
	 * @todo	Detect Dataflow module and map driver based on Content-Type.
	 * @access	protected
	 * @param	Request
	 * @return	array|Response
	 */
	protected function _filter_response(Response $response)
	{
		if ($body = $response->body())
		{
			if (is_array($body))
				return $body;
			
			if ($body instanceof Dataflow)
				return $body->get()->as_array();
			
			switch ($response->headers('Content-Type'))
			{
				case 'application/json':
					return json_decode($body, TRUE);
					
				case 'application/php':
					return unserialize($body);
			}
			
			throw new Kohana_Exception('Unable to parse Response. Unsupported Content-Type: ' . $response->headers('Content-Type'));
		}
		
		return $response;
	}
		
	/**
	 * Isset
	 * 
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function __isset($key)
	{
		return isset($this->_data[$key]);
	}
	
	/**
	 * Unset
	 * 
	 * @access	public
	 * @param	string
	 * @return	void
	 * @throws	Kohana_Exception
	 */
	public function __unset($key)
	{
		throw new Kohana_Exception('Dispatch results are read-only');
	}
	
	/**
	 * Rewind position
	 * 
	 * @see		Iterator
	 * @access	public
	 * @return	void
	 */
	public function rewind()
	{
		rewind($this->_data);
	}
	
	/**
	 * Current position
	 * 
	 * @see		Iterator
	 * @access	public
	 * @return	void
	 */
	public function current()
	{
		return current($this->_data);
	}	
	
	/**
	 * Key of current iteration
	 * 
	 * @see		Iterator
	 * @access	public
	 * @return	void
	 */
	public function key()
	{
		return key($this->_data);
	}	
	
	/**
	 * Next within the iteration
	 * 
	 * @see		Iterator
	 * @access	public
	 * @return	$this
	 */
	public function next()
	{
		next($this->_data);
		
		return $this;	
	}	
	
	/**
	 * Valid iteration
	 * 
	 * @see		Iterator
	 * @access	public
	 * @return	void
	 */
	public function valid()
	{
		return $this->offsetExists($this->key());	
	}		

	/**
	 * Exists
	 * 
	 * @see		ArrayAccess
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function offsetExists($key)
	{
		return isset($this->_data[$key]);
	}
	
	/**
	 * Get
	 * 
	 * @see		ArrayAccess
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	public function offsetGet($key)
	{
		return $this->_data[$key];
	}	
	
	/**
	 * Set
	 * 
	 * @see		ArrayAccess
	 * @access	public
	 * @param	string
	 * @param	mixed
	 * @return	void
	 * @throws	Kohana_Exception
	 */
	public function offsetSet($key, $value)
	{
		throw new Kohana_Exception('Dispatch results are read-only');
	}	
	
	/**
	 * Unset
	 * 
	 * @see		ArrayAccess
	 * @access	public
	 * @param	string
	 * @return	void
	 * @throws	Kohana_Exception
	 */
	public function offsetUnset($key)
	{
		throw new Kohana_Exception('Dispatch results are read-only');
	}	

	/**
	 * Count
	 * 
	 * @see		Countable
	 * @access	public
	 * @return	int
	 */
	public function count()
	{
		return count($this->_data);
	}	
}