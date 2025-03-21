<?php
namespace myframework;

/**
 * Главный класс приложения
 */
abstract class CApplication
{
	public array $_config;

	public array $classesList = [];

	abstract protected function _processRequest();

	public CWebRequest $request;

	public CHttpSession $session;

	public CErrorHandler $errorHandler;

	protected bool $_useRoutes = false;

	public array $routes = [];

	public function __construct(array $config)
	{
		F::$mysql = CMysqlInstance::inst();

		$this->_config = $config;

		$this->_includeExtends();

		$this->_initErrorHandlers();

		$this->_includeModels();

		$this->_includeIncludes();

		$this->_setRoutes();
	}

	protected function _includeModels()
	{
		foreach (glob(ROOT . '/models/*', GLOB_ONLYDIR) as $dir)
		{
			foreach (glob($dir . '/*.php') as $path)
			{
				$baseName = basename($path, '.php');

				$this->classesList['super\\' . $baseName] = $path;
			}
		}

		foreach (glob(ROOT . '/models/*.php') as $path)
		{
			$baseName = basename($path, '.php');

			$this->classesList['super\\' . $baseName] = $path;
		}

		foreach (glob(APP . '/models/*', GLOB_ONLYDIR) as $dir)
		{
			foreach (glob($dir . '/*.php') as $path)
			{
				$baseName = basename($path, '.php');

				$this->classesList[PROJECT . '\\' . $baseName] = $path;
			}
		}

		foreach (glob(APP . '/models/*.php') as $path)
		{
			$baseName = basename($path, '.php');

			$this->classesList[PROJECT . '\\' . $baseName] = $path;
		}
	}

	protected function _includeExtends()
	{
		foreach (glob(ROOT . '/extends/*.php') as $path)
		{
			$baseName = basename($path, '.php');

			$this->classesList['super\\' . $baseName] = $path;
		}

		foreach (glob(APP . '/extends/*.php') as $path)
		{
			$baseName = basename($path, '.php');

			$this->classesList[PROJECT . '\\' . $baseName] = $path;
		}
	}

	protected function _includeIncludes()
	{
		foreach ($this->_config['includes'] as $name)
		{
			if (is_file($file = APP . "/includes/$name"))
			{
				require_once $file;
			}
			elseif (is_file($file = ROOT . "/includes/$name"))
			{
				require_once $file;
			}
		}
	}

	protected function _setRoutes()
	{
		if ($this->_useRoutes)
		{
			$this->routes = require_once APP . '/config/routes.php';
		}
	}

	public function findRoute($path)
	{
		if ($this->_useRoutes)
		{
			foreach ($this->routes as $regex => $array)
			{
				if (preg_match("|^$regex$|", $path, $match))
				{
					$match = array_filter($match, 'is_string', ARRAY_FILTER_USE_KEY);

					return [ $array, $match ];
				}
			}
		}

		return false;
	}

	public function run(): void
	{
		register_shutdown_function([$this, 'end'], 0, false);

		$this->_processRequest();
	}

	public function end($status = 0, $exit = true)
	{
		if ($exit)
		{
			exit($status);
		}
	}

	protected function _initErrorHandlers()
	{
    set_exception_handler([$this, 'handleException']);

	  set_error_handler([$this, 'handleError'], error_reporting());
	}

}
