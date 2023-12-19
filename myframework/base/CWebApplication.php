<?php
namespace myframework;

class CWebApplication extends CApplication
{
	public function __construct(array $config)
	{
		F::$app = $this;

		parent::__construct($config);

		$this->request = new CWebRequest();

		$this->request->init();
	}

	protected function _processRequest()
	{
		list($directory, $controller, $action, $arguments) = $this->request->parseRequest();

		$this->runController($directory, $controller, $action, $arguments);
	}

	/**
	 * @param string $controller
	 * @param string $action
	 * @param array $arguments
	 * @throws CHttpException
	 */
	public function runController(string $directory, string $controller, string $action, array $arguments = []): void
	{
		$baseName = ucfirst($controller) . 'Controller';

		if (! is_file($file = APP . '/controllers/' . ($directory ? $directory . '/' : '') . $baseName . '.php'))
		{
			throw new CHttpException('Страница не найдена', 404);
		}

		require_once $file;

		$className = PROJECT . '\\' . $baseName;

		/** @var $class CController */
		$class = new $className($directory, $controller, $action);

		$class->runController($arguments, $this->request->params);
	}

}
