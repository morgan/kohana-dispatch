<?php defined('SYSPATH') or die('No direct script access.');

Route::set('dispatch/test', '<directory>/<controller>(/<id>).<format>', array('format' => '(json|php)', 'id' => '.*'))->defaults(array
(
	'directory'		=> 'dispatch', 
	'controller'	=> 'test',
	'action'		=> 'get',
));