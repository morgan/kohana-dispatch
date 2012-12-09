<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Dispatch Connection
 * 
 * @package		Dispatch
 * @category	Base
 * @author		Micheal Morgan <micheal@morgan.ly>
 * @copyright	(c) 2011-2012 Micheal Morgan
 * @license		MIT
 */
class Kohana_Dispatch_Connection
{
	/**
	 * Default connection config `dispatch.default`
	 * 
	 * @static
	 * @access	public
	 * @var		string
	 */
	public static $default = 'default';

	/**
	 * Headers
	 * 
	 * @access	protected
	 * @var		array
	 */
	protected $_headers = array();
	
	/**
	 * Configuration
	 * 
	 * @access	protected
	 * @var		array
	 */
	protected $_config = array
	(
		'url'				=> NULL,
		'extension'			=> NULL,
		'namespace'			=> NULL,
		'attempt_local'		=> TRUE,
		'headers'			=> array(),
		'external_client'	=> 'Request_Client_Curl'
	);
	
	/**
	 * Singleton pattern
	 * 
	 * @access	public
	 * @param	mixed	NULL|string
	 * @param	array
	 * @return	Dispatch_Connection
	 */
	public static function instance($name = NULL, array $config = array())
	{		
		static $instances;
		
		$name = $name ? strtolower($name) : Dispatch_Connection::$default;

		if ( ! isset($instances[$name]))
		{
			$config = Arr::merge(Kohana::$config->load('dispatch.' . $name), $config);
			
			$instances[$name] = new Dispatch_Connection($config);
		}
		
		return $instances[$name];
	}

	/**
	 * Factory pattern
	 * 
	 * @access	public
	 * @param	array
	 * @return	Dispatch_Connection
	 */
	public static function factory(array $config = array())
	{
		return new Dispatch_Connection($config);
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
		$this->_config = Arr::merge($this->_config, $config);
		
		$this->_headers =& $this->_config['headers'];
	}
	
	/**
	 * Get or set headers to be used across connection
	 * 
	 * @access	public
	 * @param	mixed	NULL|string
	 * @param	mixed
	 * @return	mixed
	 */
	public function headers($key = NULL, $value = NULL)
	{
		if ($key === NULL)
			return $this->_headers;
		
		if (is_array($key))
		{
			$this->_headers = $key;
		}
			
		$this->_headers[$key] = $value;
		
		return $this;
	}
	
	/**
	 * Request
	 * 
	 * @access	public
	 * @return	Response|FALSE
	 */
	public function execute($path, $method = Request::GET, array $query = array(), array $body = array(), array $headers = array())
	{
		$response = FALSE;
		
		$namespace = $this->_config['namespace'] ? $this->_config['namespace'] . '/' : NULL; 
		
		if ( ! empty($this->_headers))
		{
			$headers = Arr::merge($this->_headers, $headers);
		}
		
		if ($this->_config['attempt_local'])
		{
			$response = $this->_request($namespace . $path, $method, $query, $body, $headers, FALSE);
		}
		
		if ( ! $response)
		{
			$response = $this->_request($this->_config['url'] . $namespace . $path, $method, $query, $body, $headers, TRUE);
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
	protected function _request($path, $method, array $query, array $body, array $headers, $external = TRUE)
	{
		try
		{
			$query = ( ! empty($query)) ? '?' . http_build_query($query, NULL, '&') : NULL;

			$extension = ($this->_config['extension'] !== NULL) ? '.' . $this->_config['extension'] : NULL;

			$request = Request::factory($path . $extension . $query)
				->method($method);
			
			if ( ! empty($body))
			{
				$request->post($body);
			}
			
			foreach ($headers as $key => $value)
			{
				$request->headers($key, $value);
			}
			
			if ($external)
			{
				$request->client(Request_Client_External::factory(array(), $this->_config['external_client']));
			}

			return $request->execute();
		}
		catch (HTTP_Exception_404 $e) 
		{
			return FALSE;
		}
	}
}
