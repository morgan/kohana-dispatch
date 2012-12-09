<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Dispatch Request
 * 
 * @package		Dispatch
 * @category	Base
 * @author		Micheal Morgan <micheal@morgan.ly>
 * @copyright	(c) 2011-2012 Micheal Morgan
 * @license		MIT
 */
class Kohana_Dispatch_Request
{
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
	 * Headers
	 * 
	 * @access	protected
	 * @var		array
	 */
	protected $_headers = array();

	/**
	 * Connection
	 * 
	 * @access	protected
	 * @var		Dispatch_Connection
	 */
	protected $_connection;
	
	/**
	 * Factory pattern
	 * 
	 * @access	public
	 * @param	mixed	NULL|string
	 * @param	array
	 * @return	Dispatch_Request
	 */
	public static function factory()
	{		
		return new Dispatch_Request;
	}

	/**
	 * Get or set connection
	 * 
	 * @access	public
	 * @param	mixed	NULL|Dispatch_Connection
	 * @return	Dispatch_Connection
	 */
	public function connection(Dispatch_Connection $connection = NULL)
	{
		if ($connection === NULL)
		{
			if ($this->_connection === NULL)
			{
				$this->_connection = Dispatch_Connection::instance();
			}
		}
		else
		{
			$this->_connection = $connection;
		}
		
		return $this->_connection;
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
	 * Get or set headers
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
	public function execute($method = Request::GET)
	{
		return $this->connection()->execute($this->path(), $method, $this->_query, $this->_data, $this->_headers);
	}
}
