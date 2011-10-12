<?php defined('SYSPATH') OR die('Kohana bootstrap needs to be included before tests run');
/**
 * Tests Dispatch Module
 *
 * @see			Controller_Dispatch_Test
 * @package		Dispatch
 * @category	Tests
 * @author		Micheal Morgan <micheal@morgan.ly>
 * @copyright	(c) 2011 Micheal Morgan
 * @license		MIT
 */
class Kohana_DispatchTest extends Unittest_TestCase
{
	/**
	 * Data provider for Request
	 *
	 * @access	public
	 * @return	array
	 */
	public static function provider_request()
	{
		return array
		(
			array
			(
				array
				(
					'namespace'		=> 'dispatch',
					'extension'		=> 'json',
					'attempt_local'	=> TRUE
				)
			),
			array
			(
				array
				(
					'url'			=> URL::site(),
					'namespace'		=> 'dispatch',
					'extension'		=> 'php',
					'attempt_local'	=> FALSE
				)
			)
		);
	}	
	
	/**
	 * Tests request
	 * 
	 * @dataProvider	provider_request
	 * @access			public
	 * @return			void
	 */	
	public function test_request($config)
	{
		$methods = array(Request::GET, Request::POST, Request::PUT, Request::DELETE);
		
		foreach ($methods as $method)
		{			
			$dispatch = Dispatch::factory('test', NULL, $config);
			
			$response = $dispatch->execute($method);
			
			$this->assertSame($response->loaded(), TRUE);
		}
	}
}