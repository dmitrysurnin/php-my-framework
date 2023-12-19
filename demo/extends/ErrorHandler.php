<?php
namespace super;

class ErrorHandler extends \myframework\CErrorHandler
{
	use \myframework\CRender;

	public string $controller = 'error';

	public string $title = 'System Error';

	public string $action;

	protected function renderError(string $view, array $data): void
	{
		if (! CONSOLE && ! $this->errorShown)
		{
			$this->errorShown = true;

			if (DEBUG) /// отобразить развёрнутый лог ошибки
			{
				parent::renderError('error', $data);
			}
			elseif ($this->stopExecution) /// отобразить скромное сообщение "ведутся технические работы"
			{
				$this->action = 'error';

				$this->renderPage('default', 'error', $data);
			}
		}
		elseif (CONSOLE)
		{
			echo "\nerror: " . $data['message'] . "\nfile: " . $data['file'] . ':' . $data['line'] . "\n";
		}
	}

}
