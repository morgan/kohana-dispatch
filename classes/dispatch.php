<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Dispatch Pattern for internal and remote REST services
 * 
 * @package		Dispatch
 * @category	Base
 * @author		Micheal Morgan <micheal@morgan.ly>
 * @copyright	(c) 2011 Micheal Morgan
 * @license		MIT
 */
class Dispatch
{	
	/**
	 * URL to remote web service
	 * 
	 * @access	protected
	 * @var		NULL|string
	 */
	protected $_url;
	
	/**
	 * File extension
	 * 
	 * @access	protected
	 * @var		NULL|string
	 */
	protected $_extension;
	
	/**
	 * Path to controller
	 * 
	 * @access	protected
	 * @var		NULL|string
	 */
	protected $_path;	
	
	/**
	 * Namespace to prepend path
	 * 
	 * @access	protected
	 * @var		NULL|string
	 */
	protected $_namespace;
	
	/**
	 * Whether or not to check for local route before making remote request
	 * 
	 * @access	protected
	 * @var		bool
	 */
	protected $_attempt_local = TRUE;

	/**
	 * Data for query string
	 * 
	 * @access	protected
	 * @var		array
	 */
	protected $_query = array();
	
	/**
	 * Data for body
	 * 
	 * @access	protected
	 * @var		array
	 */
	protected $_data = array();
	
	/**
	 * Factory pattern
	 * 
	 * @access	public
	 * @return	Dispatch
	 */
	public static function factory($path)
	{
		return new Dispatch($path);
	}

	/**
	 * Initialization
	 * 
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	public function __construct($path)
	{
		$this->_path = $path;
	}

	/**
	 * GET
	 * 
	 * @access	public
	 * @return	$this
	 */
	public function find()
	{
		return $this->execute('GET');
	}
	
	/**
	 * POST
	 * 
	 * @access	public
	 * @return	$this
	 */	
	public function create()
	{
		return $this->execute('POST');
	}
	
	/**
	 * PUT
	 * 
	 * @access	public
	 * @return	$this
	 */	
	public function update()
	{
		return $this->execute('PUT');
	}
	
	/**
	 * DELETE
	 * 
	 * @access	public
	 * @return	$this
	 */	
	public function delete()
	{
		return $this->execute('DELETE');
	}	
	
	/**
	 * Return path
	 * 
	 * @access	public
	 * @return	string
	 */
	public function path()
	{
		return $this->_path;
	}

	/**
	 * Add to query string if set or return
	 * 
	 * @access	public
	 * @return	$this
	 */
	public function where($data = NULL, $value = NULL)
	{
		if ($this->_query === NULL)
			return $this->_query;
		
		if ( ! is_array($data))
		{
			$data = array($data => $value);
		}
		
		foreach ($data as $key => $value)
		{
			$this->_query[$key] = $value;
		}
		
		return $this;
	}

	/**
	 * Set data
	 * 
	 * @access	public
	 * @return	$this
	 */
	public function set($data, $value = NULL)
	{
		if ( ! is_array($data))
		{
			$data = array($data => $value);
		}
		
		foreach ($data as $key => $value)
		{
			$this->_data[$key] = $value;
		}
		
		return $this;
	}

	/**
	 * Get data
	 * 
	 * @access	public
	 * @return	mixed
	 */
	public function get($key = NULL)
	{
		if ($key === NULL)
			return $this->_data;
			
		if (isset($this->_data[$key]))
			return $this->_data[$key];
		
		return NULL;
	}
	
	/**
	 * Request
	 * 
	 * @access	public
	 * @return	Response|FALSE
	 */
	public function execute($method = 'GET')
	{
		$response = FALSE;
		
		if ($this->_attempt_local)
		{	
			$response = $this->_request($this->_namespace . $this->_path, $method);
		}
		
		if ( ! $response)
		{
			$response = $this->_request($this->_url . $this->_namespace . $this->_path, $method);
		}
			
		if ($response instanceof Response)
			return $this->_filter_response($response);
		
		return FALSE;
	}
	
	/**
	 * Generate request
	 * 
	 * @access	protected
	 * @param	string
	 * @return	Response|FALSE
	 */
	protected function _request($path, $method)
	{
		try
		{
			$query = ( ! empty($this->_query)) ? '?' . http_build_query($this->_query, NULL, '&') : NULL;

			$extension = ($this->_extension !== NULL) ? '.' . $this->_extension : NULL;
				
			return Request::factory($path . $extension . $query)
				->method($method)
				->post($this->get())
				->execute();
		}
		catch (HTTP_Exception_404 $e) 
		{
			return FALSE;
		}
	}	
	
	/**
	 * Filter Response
	 * 
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
		}
		
		return $response;
	}
}