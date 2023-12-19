<?php
namespace myframework;

/**
 */
class CWebRequest
{
	/* Протокол: "http" либо "https" */
	public string $protocol;

	/* Имя сервера, указано в переменной $_SERVER['SERVER_NAME'], например: "dev.example.com" */
	public string $server;

	/* Имя домена, указано в конфиге nginx (переменная $_SERVER['DOMAIN_NAME']), например: "example.com" */
	public string $domain;

	/* protocol + "://" + server, например: "https://dev.example.com" */
	public string $host;

	/* protocol + "://" + domain, например: "https://example.com" */
	public string $home;

	/* protocol + "://www." + domain, например: "https://www.example.com" */
	public string $www;

	/* Указано в переменной $_SERVER['REQUEST_URI'], например: "/test/param/?get_param=123" */
	public string $uri;

	/* host + uri = полный url, например: "https://dev.example.com/test/param/?get_param=123" */
	public string $url;

	/* uri, но без GET-параметров, например: "/test/param/" */
	public string $uriBase;

	/* uri, но без GET-параметров и слешей по краям, например: "test/param" */
	public string $path;

	public bool $isAjax; /// является ли этот запрос ajax-запросом

	public bool $isPost; /// является ли этот запрос post-запросом

	/* С какого ip пришёл запрос */
	public string $ip;

	/**
	 * Переданные параметры POST и GET, = array_replace_recursive($_POST, $_GET)
	 */
	public array $params;

	private string $_defaultController = 'welcome';

	private string $_defaultAction = 'index';

	private string $_url;

	private string $_host;

	public function __construct()
	{
	}

	public function init()
	{
		$this->uri = $_SERVER['REQUEST_URI'];

		$this->uriBase = strtok($this->uri, '?');

		$this->path = rtrim($this->uriBase, '/');

		$this->path === '' and $this->path = '/';

		$this->server = $_SERVER['SERVER_NAME'];

		$this->domain = $_SERVER['DOMAIN_NAME'] ?? '';

		$this->protocol = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '');

		$this->host = $this->protocol . '://' . $this->server;

		$this->home = $this->protocol . '://' . $this->domain;

		$this->www = $this->protocol . '://www.' . $this->domain;

		$this->url = $this->host . $this->uri;

		$this->params = array_replace_recursive($_POST, $_GET);

		$this->isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

		$this->isPost = $_SERVER['REQUEST_METHOD'] === 'POST';

		$this->ip = $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * @return array
	 */
	public function parseRequest()
	{
		if ($this->path === '/')
		{
			$directory = '';

			$controller = $this->_defaultController;

			$action = $this->_defaultAction;

			$arguments = [];
		}
		else
		{
			$path = $this->path;

			$route = F::$app->findRoute($path);

			if ($route)
			{
				list($array, $match) = $route;

				$directory = $array['directory'];

				$controller = $array['controller'];

				$action = $array['action'];

				$arguments = $match;
			}
			else
			{
				$directory = '';

				$arguments = explode('/', ltrim($path, '/'));

				$controller = array_shift($arguments);

				$action = $arguments ? array_shift($arguments) : $this->_defaultAction;
			}
		}

		return [ $directory, $controller, $action, $arguments ];
	}

	public function data($name)
	{
		if (isset($this->params[$name]))
		{
			return $this->params[$name];
		}

		return null;
	}

	public function getUrl()
	{
		if ($this->_url === null)
		{
			$this->_url = $_SERVER['REQUEST_URI'];
		}

		return $this->_url;
	}

	public function getHost()
	{
		if ($this->_host === null)
		{
			$this->_host = $_SERVER['HTTP_HOST'];
		}

		return $this->_host;
	}

	public function redirect(string $url, array $data = [], int $code = 301): void
	{
		header('Location: ' . $url . ($data ? '?' . http_build_query($data) : ''), true, $code);

		exit;
	}

	public function post($name = null): array|string|null
	{
		if ($name !== null)
		{
			return $_POST[$name] ?? null;
		}

		return $_POST;
	}

	public function file($name): ?array
	{
		return $_FILES[$name] ?? null;
	}

}
