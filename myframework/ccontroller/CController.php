<?php
namespace myframework;

class CController
{
	use CRender
	{
		render as renderTrait;
		renderPage as renderPageTrait;
	}

	use CControllerHtmlInputs;

	public string $directory = ''; /// директория контроллера

	public string $controller; /// имя контроллера

	public string $action; /// метод

	public CApplication $app;

	public CWebRequest $request;

	public CHttpSession $session;

	protected bool $_startSession = true; /// разрешено ли стартовать сессию. для некоторых контроллеров (вывод картинок например) это запрещено

	public string $pathView = '';

	public string $title = '';

	public array $head = [];

	public string $keywords = '';

	public string $description = '';

	protected string $template = 'default';

	protected array $_addToTemplate = [];

	public function __construct(string $directory, string $controller, string $action)
	{
		$this->directory = $directory;

		$this->controller = $controller;

		$this->action = $action;

		$this->app = F::$app;

		$this->request = F::$app->request;

		$this->session = F::$app->session;
	}

	protected function _before()
	{
		if ($this->_startSession) /// иначе не будем использовать сессию, это нужно для контроллеров, отдающих картинки например
		{
			$this->session->start();
		}
	}

	protected function _after()
	{
	}

	protected function render(string $view, array $data = [], $return = false)
	{
		substr($view, 0, 1) !== '/' and $view = $this->pathView . ($this->directory ? lcfirst($this->directory) . '/' : '') . lcfirst($this->controller) . '/' . lcfirst($view);

		$data += $this->_addToTemplate;

		return $this->renderTrait($view, $data, $return);
	}

	protected function renderPage(string $view, array $data = [], bool $return = false)
	{
		substr($view, 0, 1) !== '/' and $view = $this->pathView . ($this->directory ? lcfirst($this->directory) . '/' : '') . lcfirst($this->controller) . '/' . lcfirst($view);

		$data += $this->_addToTemplate;

		return $this->renderPageTrait($this->template, $view, $data, $return);
	}

	protected function renderData(string $view, array $data = [], bool $return = false)
	{
		if ($this->request->isAjax)
		{
			return $this->render($view, $data, $return);
		}
		else
		{
			return $this->renderPage($view, $data, $return);
		}
	}

	public function runController(array $arguments = [], array $params = [])
	{
		$this->_before();

		$this->_runAction($arguments, $params);

		$this->_after();
	}

	protected function _runAction($arguments = [], $params = [])
	{
		$method = 'action' . ucfirst($this->action);

		if (method_exists($this, $method))
		{
			$reflectionMethod = new \ReflectionMethod($this, $method);

			if ($reflectionMethod->getNumberOfParameters() > 0)
			{
				return $this->_runMethodWithParams($reflectionMethod, $arguments, $params);
			}
			else
			{
				return $this->$method();
			}
		}
		else
		{
			throw new CHttpException('Страница не найдена', 404);
		}
	}

	protected function _runMethodWithParams(\ReflectionMethod $reflectionMethod, $arguments, $params)
	{
		$p = [];

		foreach ($reflectionMethod->getParameters() as $param)
		{
			$name = $param->getName();

			if (isset($params[$name]))
			{
				if ($type = $param->getType() and $type->getName() === 'array')
				{
					$p[] = is_array($params[$name]) ? $params[$name] : [$params[$name]];
				}
				else
				{
					$p[] = $params[$name];
				}
			}
			elseif ($arguments)
			{
				$p[] = array_shift($arguments);
			}
			elseif ($param->isDefaultValueAvailable())
			{
				$p[] = $param->getDefaultValue();
			}
			else
			{
				throw new \Exception('Wrong params');
			}
		}

		return $reflectionMethod->invokeArgs($this, $p);
	}

	public function redirect(string $url = '/', array $data = [], int $code = 301)
	{
		$this->request->redirect($url, $data, $code);
	}

	public function addCss(string $path): void
	{
		$this->head[] = '<link rel="stylesheet" href="/css/' . $path . '"/>';
	}

	public function addJs(string $path): void
	{
		$this->head[] = '<script type="text/javascript" src="' . $path . '"></script>';
	}

	protected function _assert($condition, $message = '')
	{
		if ((bool) $condition === false)
		{
			throw new CAssertException($message);
		}
	}

}
