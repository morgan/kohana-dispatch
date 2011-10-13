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
	 * Provider for test_path
	 *
	 * @access	public
	 * @return	array
	 */
	public static function provider_path()
	{
		return array
		(
			array
			(
				array('user', 2, 'email', 5),
				'user/2/email/5'
			),
			array
			(
				'account/3/user/2',
				'account/3/user/2'
			)
		);
	}	
	
	/**
	 * Test path handling
	 * 
	 * @covers			Dispatch::factory
	 * @covers			Dispatch_Request::path
	 * @dataProvider	provider_path
	 * @access			public
	 * @return			void
	 */
	public function test_path($provided, $expected)
	{
		// Test Dispatch::factory
		$this->assertSame(Dispatch::factory($provided)->path(), $expected);

		// Test Dispatch_Request::path
		$dispatch = new Dispatch_Request;
		
		$dispatch->path($provided);
		
		$this->assertSame($dispatch->path(), $expected);
	}
	
	/**
	 * Internal and external configuration to test consistency across 
	 * Client-Dispatcher-Server pattern.
	 *
	 * @access	public
	 * @return	array
	 */
	public static function provider_config()
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
	 * Tests HTTP code handling
	 * 
	 * @covers			Dispatch::factory
	 * @covers			Dispatch_Request::factory
	 * @covers			Dispatch_Response::factory
	 * @covers			Dispatch_Request::where
	 * @covers			Dispatch_Response::loaded
	 * @dataProvider	provider_config
	 * @access			public
	 * @return			void
	 */	
	public function test_http_code($config)
	{
		$dispatch = Dispatch::factory('test', NULL, $config);

		$this->assertTrue($dispatch->find()->loaded(), 'Expecting Test resource to have loaded.');
		
		$dispatch->where('code', 500);
		
		$this->assertFalse($dispatch->find()->loaded(), 'Invalid HTTP code should not validate as a loaded resource.');
	}	
	
	/**
	 * Tests request
	 * 
	 * @covers			Dispatch::factory
	 * @covers			Dispatch_Request::factory
	 * @covers			Dispatch_Response::factory
	 * @covers			Dispatch_Request::execute
	 * @covers			Dispatch_Response::loaded
	 * @dataProvider	provider_config
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