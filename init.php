<?php defined('SYSPATH') or die('No se permite acceso directo al script.');

// Archivo estática del servidor (CSS, JS, images)
Route::set('docs/media', 'guide/media(/<file>)', array('file' => '.+'))
	->defaults(array(
		'controller' => 'userguide',
		'action'     => 'media',
		'file'       => NULL,
	));

if (Kohana::config('userguide.api_browser') === TRUE)
{
	// API Browser
	Route::set('docs/api', 'guide/api(/<class>)', array('class' => '[a-zA-Z0-9_]+'))
		->defaults(array(
			'controller' => 'userguide',
			'action'     => 'api',
			'class'      => NULL,
		));
}

// Traducción guía del usuario
Route::set('docs/guide', 'guide(/<page>)', array(
		'page' => '.+',
	))
	->defaults(array(
		'controller' => 'userguide',
		'action'     => 'docs',
	));

