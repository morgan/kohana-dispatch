<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Dispatch Request
 * 
 * @package		Dispatch
 * @category	Base
 * @author		Micheal Morgan <micheal@morgan.ly>
 * @copyright	(c) 2011 Micheal Morgan
 * @license		MIT
 */
class Kohana_Dispatch_Request
{	
	/**
	 * Default connection config `dispatch.default`
	 * 
	 * @static
	 * @access	public
	 * @var		string
	 */
	public static $connection = 'default';
	
	/**
	 * Default external client
	 * 
	 * @static
	 * @access	public
	 * @var		string
	 */
	public static $external_client = 'Request_Client_Curl';
	
	/**
	 * Path to controller
	 * 
	 * @access	protected
	 * @var		NULL|string
	 */
	protected $_path;	

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
	 * Configuration
	 * 
	 * @access	protected
	 * @var		array
	 */
	protected $_config = array
	(
		'url'			=> NULL,
		'extension'		=> NULL,
		'namespace'		=> NULL,
		'attempt_local'	=> TRUE
	);
	
	/**
	 * Factory pattern
	 * 
	 * @access	public
	 * @param	mixed	NULL|string
	 * @param	array
	 * @return	Dispatch_Request
	 */
	public static function factory($connection = NULL, array $config = array())
	{		
		$connection = $connection ? strtolower($connection) : Dispatch_Request::$connection;

		$config = $config + Kohana::$config->load('dispatch.' . $connection);
		
		return new Dispatch_Request($config);
	}

	/**
	 * Initialization
	 * 
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	public function __construct(array $config = array())
	{		
		$this->_config = $config + $this->_config;
	}

	/**
	 * Getter and setter for path
	 * 
	 * @access	public
	 * @param	mixed	NULL|string|array
	 * @return	mixed	$this|string
	 */
	public function path($path = NULL)
	{
		if ($path === NULL)
			return $this->_path;
			
		if (is_array($path))
		{
			$path = implode('/', $path);
		}
		
		$this->_path = $path;
		
		return $this;
	}
	
	/**
	 * GET
	 * 
	 * @access	public
	 * @return	$this
	 */
	public function find()
	{
		return $this->execute(Request::GET);
	}
	
	/**
	 * POST
	 * 
	 * @access	public
	 * @return	$this
	 */	
	public function create()
	{
		return $this->execute(Request::POST);
	}
	
	/**
	 * PUT
	 * 
	 * @access	public
	 * @return	$this
	 */	
	public function update()
	{
		return $this->execute(Request::PUT);
	}
	
	/**
	 * DELETE
	 * 
	 * @access	public
	 * @return	$this
	 */	
	public function delete()
	{
		return $this->execute(Request::DELETE);
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
	public function execute($method = Request::GET)
	{
		$response = FALSE;
		
		$namespace = $this->_config['namespace'] ? $this->_config['namespace'] . '/' : NULL; 
		
		if ($this->_config['attempt_local'])
		{	
			$response = $this->_request($namespace . $this->_path, $method);
		}
		
		if ( ! $response)
		{
			$response = $this->_request($this->_config['url'] . $namespace . $this->_path, $method, TRUE);
		}

		return Dispatch_Response::factory($response);
	}
	
	/**
	 * Generate request
	 * 
	 * @access	protected
	 * @param	string
	 * @return	Response|FALSE
	 */
	protected function _request($path, $method, $external = FALSE)
	{
		try
		{
			$query = ( ! empty($this->_query)) ? '?' . http_build_query($this->_query, NULL, '&') : NULL;

			$extension = ($this->_config['extension'] !== NULL) ? '.' . $this->_config['extension'] : NULL;
				
			$request = Request::factory($path . $extension . $query)
				->method($method)
				->post($this->get());
			
			if ($external)
			{
				$request->client(Request_Client_External::factory(array(), Dispatch_Request::$external_client));
			}	
				
			return $request->execute();
		}
		catch (HTTP_Exception_404 $e) 
		{
			return FALSE;
		}
	}
}