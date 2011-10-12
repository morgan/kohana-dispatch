<?php defined('SYSPATH') or die('No direct script access.');
/**
 * dispatch/test
 * 
 * @package		Dispatch
 * @category	Base
 * @author		Micheal Morgan <micheal@morgan.ly>
 * @copyright	(c) 2011 Micheal Morgan
 * @license		MIT
 */
class Controller_Dispatch_Test extends Controller
{
	/**
	 * Basic HTTP method mapping. Normalize POST and PUT operations.
	 * 
	 * @access	public
	 * @return	void
	 */
	public function before()
	{
		$this->_action_requested = $this->request->action();

		$method = Arr::get($_SERVER, 'HTTP_X_HTTP_METHOD_OVERRIDE', $this->request->method());

		if (method_exists($this, $method))
		{
			$this->request->action('action_' . $method);
		}
		
		if ($this->request->method() == Request::PUT)
		{
			parse_str($this->request->body(), $post);
		
			$this->request->post($post);
		}		
	}
	
	/**
	 * GET dispatch/test
	 * 
	 * @access	public
	 * @return	void
	 */
	public function action_get()
	{
		$this->_render(array
		(
			'method'	=> Request::GET,
			'data'		=> $this->request->query()
		));
	}

	/**
	 * POST dispatch/test
	 * 
	 * @access	public
	 * @return	void
	 */
	public function action_post()
	{
		$this->_render(array
		(
			'method'	=> Request::POST,
			'data'		=> $this->request->post()
		));
	}

	/**
	 * PUT dispatch/test
	 * 
	 * @access	public
	 * @return	void
	 */
	public function action_put()
	{
		$this->_render(array
		(
			'method'	=> Request::PUT,
			'data'		=> $this->request->post()
		));
	}

	/**
	 * DELETE dispatch/test
	 * 
	 * @access	public
	 * @return	void
	 */
	public function action_delete()
	{
		$this->_render(array
		(
			'method'	=> Request::DELETE,
			'data'		=> $this->request->query()
		));
	}
	
	/**
	 * Test basic flow of output based on file extension.
	 * 
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	protected function _render($data, $status = 200)
	{
		$data = ( ! is_array($data)) ? array('message' => (string) $data) : $data;
		
		switch ($this->request->param('format'))
		{
			case 'json':
				$data = json_encode($data);
				$this->response->headers('content-type', 'application/json');
				break;
				
			case 'php':
				$data = serialize($data);
				$this->response->headers('content-type', 'application/php');
				break;
		}		
		
		$this->response
			->status($status)
			//->headers('cache-control', 'no-cache, no-store, max-age=0, must-revalidate')
			->body($data);
	}	
}