<?php

return [

	/// по умолчанию роут равен /controller/action, но здесь можно изменить это поведение
	/// пример своего роута, доступен по адресу /hello/world
	'/hello/world/?' => [
		'directory' => '',
		'controller' => 'Welcome',
		'action' => 'Index',
	],

];
