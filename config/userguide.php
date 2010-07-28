<?php defined('SYSPATH') OR die('No se permite acceso directo al script.');

return array
(
	// Página predeterminada UserGuide.
	'default_page' => 'about.kohana',

	// El idioma por defecto para UserGuide.
	'lang'         => 'es-es',
	
	// Habilitar el explorador de la API.  TRUE or FALSE
	'api_browser'  => TRUE,
	
	// Habilitar estos paquetes en el explorador de la API.  TRUE para todos los paquetes, o una cadena de paquetes separados por comas, con "None", una clase sin un @paquete
	// Example: 'api_packages' => 'Kohana,Kohana/Database,Kohana/ORM,None',
	'api_packages' => TRUE,
);
