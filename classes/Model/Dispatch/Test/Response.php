<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Dispatch Model Test
 * 
 * @see			Controller_Dispatch_Test
 * @package		Dispatch
 * @category	Base
 * @author		Micheal Morgan <micheal@morgan.ly>
 * @copyright	(c) 2011-2012 Micheal Morgan
 * @license		MIT
 */
class Model_Dispatch_Test_Response extends Model
{
	/**
	 * Data
	 * 
	 * @access	protected
	 * @var		array
	 */
	protected $_data = array();
	
	/**
	 * Format
	 * 
	 * @access	protected
	 * @var		string
	 */
	protected $_format = 'json';
	
	/**
	 * Setter/getter for format
	 * 
	 * @access	public
	 * @param	NULL|string
	 * @return	$this|string
	 */
	public function format($format)
	{
		if ($format === NULL)
			return $this->_format;
			
		$this->_format = $format;
			
		return $this;
	}
	
	/**
	 * Set data
	 * 
	 * @access	protected
	 * @param	array
	 * @return	$this
	 */
	public function set(array $data)
	{
		$this->_data = $data;
		
		return $this;
	}
	
	/**
	 * Get data as supported format
	 * 
	 * @access	public
	 * @return	string
	 * @throws	Kohana_Exception
	 */
	public function get()
	{
		switch ($this->_format)
		{
			case 'json':
				return json_encode($this->_data);
			
			case 'php':
				return serialize($this->_data);
		}

		throw new Kohana_Exception('Unsupported format: ' . $this->_format);
	}
	
	/**
	 * Get data as array
	 * 
	 * @access	public
	 * @return	array
	 */
	public function as_array()
	{
		return $this->_data;
	}
	
	/**
	 * Convert to string based on desired format
	 * 
	 * @access	public
	 * @return	string
	 */
	public function __toString()
	{
		return $this->get();
	}
}
