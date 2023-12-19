<?php
namespace myframework;

class CErrorHandler extends CSingleton
{
	protected bool $errorShown = false;

	protected bool $stopExecution = false;

	public array $head = [];

	/**
	 * @param \Throwable $exception
	 */
	public function handleException(\Throwable $exception)
	{
		$this->stopExecution = true;

		$data = [
			'code' => $exception->getCode(),
			'type' => get_class($exception),
			'message' => $exception->getMessage(),
			'file' => $exception->getFile(),
			'line' => $exception->getLine(),
			'trace' => $exception->getTraceAsString(),
			'traces' => $exception->getTrace(),
		];

		if ($exception->getCode() != 404)
		{
			$this->notifyAboutError($data);
		}

		$this->renderError('error', $data);
	}

	/**
	 * @param $code
	 * @param $message
	 * @param $file
	 * @param $line
	 */
	public function handleError($code, $message, $file, $line)
	{
		switch ($code)
		{
			case E_ERROR:
				$type = 'Fatal error';
				break;

			case E_WARNING:
				$type = 'PHP warning';
				break;

			case E_PARSE:
				$type = 'PHP Parse error';
				break;

			case E_NOTICE:
				$type = 'PHP notice';
				break;

			case E_COMPILE_ERROR:
				$type = 'Compile error';
				break;

			case E_COMPILE_WARNING:
				$type = 'Compile warning';
				break;

			case E_USER_ERROR:
				$type = 'User error';
				break;

			case E_USER_WARNING:
				$type = 'User warning';
				break;

			case E_USER_NOTICE:
				$type = 'User notice';
				break;

			case E_STRICT:
				$type = 'Strict Standards error';
				break;

			case E_RECOVERABLE_ERROR:
				$type = 'Recoverable error';
				break;

			case E_DEPRECATED:
				$type = 'Deprecated error';
				break;

			default:
				$type = 'PHP error';
		}

		$this->stopExecution = $code == E_ERROR; /// только E_ERROR является run-time-ошибкой, которая останавливает выполнение кода

		$traces = debug_backtrace();

		$trace = $this->createErrorTrace($traces);

		$data = [
			'code' => $code,
			'type' => $type,
			'message' => $message,
			'file' => $file,
			'line' => $line,
			'trace' => $trace,
			'traces' => $traces,
		];

		$this->notifyAboutError($data);

		$this->renderError('error', $data);
	}

	protected function notifyAboutError(array $data)
	{
		/// переопределить в наследнике
		/// todo: тут нотификация разработчика об ошибке (на почту или в тг)
	}

	protected function renderError(string $view, array $data)
	{
		if (F::$app->request->isAjax)
		{
			unset($data['traces']);

			echo json_encode($data);
		}
		else
		{
			include F_ROOT . '/views/' . $view . '.php';
		}
	}

	protected function renderSourceCode($file, $errorLine, $maxLines): string
	{
		$errorLine --; /// adjust line number to 0-based from 1-based

		if ($errorLine < 0 || ($lines = @file($file)) === false || ($lineCount = count($lines)) <= $errorLine)
		{
			return '';
		}

		$halfLines = (int) ($maxLines / 2);

		$beginLine = $errorLine - $halfLines > 0 ? $errorLine - $halfLines : 0;

		$endLine = $errorLine + $halfLines < $lineCount ? $errorLine + $halfLines : $lineCount - 1;

		$lineNumberWidth = strlen($endLine + 1);

		$output = '';

		for ($i = $beginLine; $i <= $endLine; $i ++)
		{
			$isErrorLine = $i === $errorLine;

			$code = sprintf("<span class=\"ln" . ($isErrorLine ? ' error-ln' : '') . "\">%0{$lineNumberWidth}d</span> %s", $i + 1, str_replace("\t", '    ', htmlspecialchars($lines[$i], ENT_QUOTES)));

			if (! $isErrorLine)
			{
				$output .= $code;
			}
			else
			{
				$output .= '<span class="error">' . $code . '</span>';
			}
		}

		return '<div class="code"><pre>' . $output . '</pre></div>';
	}

	protected function isCoreCode($trace): bool
	{
		if (isset($trace['file']))
		{
			$systemPath = realpath(dirname(__FILE__) . '/..');

			return $trace['file'] === 'unknown' || strpos(realpath($trace['file']), $systemPath . DIRECTORY_SEPARATOR) === 0;
		}

		return false;
	}

	protected function argumentsToString($args): string
	{
		$count = 0;

		$isAssoc = $args !== array_values($args);

		foreach ($args as $key => $value)
		{
			$count ++;

			if ($count >= 5)
			{
				if ($count > 5)
				{
					unset($args[$key]);
				}
				else
				{
					$args[$key] = '...';
				}

				continue;
			}

			if (is_object($value))
			{
				$args[$key] = get_class($value);
			}
			elseif (is_bool($value))
			{
				$args[$key] = $value ? 'true' : 'false';
			}
			elseif (is_string($value))
			{
				if (strlen($value) > 64)
				{
					$args[$key] = '"' . substr($value, 0, 64) . '..."';
				}
				else
				{
					$args[$key] = '"' . $value . '"';
				}
			}
			elseif (is_array($value))
			{
				$args[$key] = 'array(' . $this->argumentsToString($value) . ')';
			}
			elseif ($value === null)
			{
				$args[$key] = 'null';
			}
			elseif (is_resource($value))
			{
				$args[$key] = 'resource';
			}

			if (is_string($key))
			{
				$args[$key] = '"' . $key.'" => ' . $args[$key];
			}
			elseif ($isAssoc)
			{
				$args[$key] = $key . ' => ' . $args[$key];
			}
		}

		$out = implode(", ", $args);

		return $out;
	}

	protected function createErrorTrace(array &$traces): string
	{
		$trace = '';

		foreach ($traces as $i => $t)
		{
			if (! isset($t['file']))
			{
				$traces[$i]['file'] = 'unknown';
			}

			if (! isset($t['line']))
			{
				$traces[$i]['line'] = 0;
			}

			if (! isset($t['function']))
			{
				$traces[$i]['function'] = 'unknown';
			}

			$trace .= "#$i {$traces[$i]['file']}({$traces[$i]['line']}): ";

			if(isset($t['object']) && is_object($t['object']))
			{
				$trace .= get_class($t['object']) . '->';
			}

			$trace .= "{$traces[$i]['function']}()\n";

			unset($traces[$i]['object']);
		}

		return $trace;
	}

}
