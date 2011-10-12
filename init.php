<?php defined('SYSPATH') or die('No direct script access.');

Route::set('dispatch/test', '<directory>(/<controller>(/<action>)).<format>', array('format' => '(json|php)'))->defaults(array
(
	'directory'		=> 'dispatch', 
	'controller'	=> 'test',
	'action'		=> 'get',
));