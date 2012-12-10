<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Dispatch Response
 * 
 * @package		Dispatch
 * @category	Base
 * @author		Micheal Morgan <micheal@morgan.ly>
 * @copyright	(c) 2011-2012 Micheal Morgan
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
		
		$this->_data = ($this->_response instanceof Response) 
			? $this->_filter_response($this->_response) 
			: array();
	}
	
	/**
	 * Whether or not response is loaded
	 * 
	 * @access	public
	 * @return	bool
	 */
	public function loaded()
	{
		if ($status = $this->status())
		{
			if ($status >= 200 && $status <= 300)
				return TRUE;
		}
		
		return FALSE;
	}

	/**
	 * Get Response status
	 * 
	 * @access	public
	 * @return	int|bool
	 */
	public function status()
	{
		if ($this->_response instanceof Response)
			return $this->_response->status();
		
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
	 * @access	protected
	 * @param	Request
	 * @return	array
	 * @throws	Kohana_Exception
	 */
	protected function _filter_response(Response $response)
	{
		// Check if Response::get_body exists for pass-through detection
		// Prevents unnecessary encoding and decoding for internal requests (when available)
		if (method_exists($response, 'get_body') AND $body = $response->get_body())
		{
			if (is_array($body))
				return $body;
			
			if ($body instanceof Dataflow)
				return $body->as_array();
		}
		
		$body = $response->body();
		
		// No need to decode an empty body, simply return array
		if (trim($body) == '' OR ! $body)
			return array();
		
		// Get MIME
		$mime = current(explode(';', $response->headers('Content-Type')));

		if ($mime AND class_exists('Dataflow') AND $driver = File::ext_by_mime($mime))
		{
			try
			{
				return Dataflow_Decode::factory(array('driver' => $driver))
					->set($body)
					->get();
			}
			catch (Kohana_Exception $e) {}
		}

		// Basic native decoding based on Content-Type header
		switch ($mime)
		{
			case 'application/json':
				return json_decode($body, TRUE);
				
			case 'application/x-httpd-php':
				return unserialize($body);
		}

		// If unable to parse Response, throw exception
		throw new Kohana_Exception('Unable to parse Response. Unsupported MIME: ' . $mime);
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
