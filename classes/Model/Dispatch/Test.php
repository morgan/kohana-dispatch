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
class Model_Dispatch_Test extends Model
{
	/**
	 * Data
	 * 
	 * @access	protected
	 * @var		array
	 */
	protected $_data = array
	(
		array
		(
			'id'	=> 1,
			'label' => 'Test 1'
		),
		array
		(	
			'id'	=> 2,
			'label' => 'Test 2'
		)
	);
	
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
	 */
	public function get($id = NULL)
	{
		if ($id === NULL)
			return $this->_data;
		
		if (isset($this->_data[--$id]))
			return $this->_data[$id];
			
		return FALSE;
	}
}
