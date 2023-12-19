<?php
namespace myframework;

class CConsoleCommand
{
	protected $_name;

	protected $_runner;

	protected $_defaultAction = 'index';

	protected $_error = '';

	protected $_message;

	protected $_notices = [];

	public function __construct($name, $runner)
	{
		$this->_name = $name;

		$this->_runner = $runner;

		set_error_handler([$this, 'handleError'], error_reporting());
	}

	public function init()
	{
	}

	public function run(array $args = [])
	{
		list($action, $options, $args) = $this->resolveRequest($args);

		$methodName = 'action' . ucfirst($action);

		$method = new \ReflectionMethod($this, $methodName);

		$params = [];

		$methodParams = $method->getParameters();

		foreach ($methodParams as $i => $param)
		{
			$name = $param->getName();

			if (isset($options[$name]))
			{
				if ($type = $param->getType() and $type->getName() === 'array')
				{
					$params[$name] = is_array($options[$name]) ? $options[$name] : [$options[$name]];
				}
				elseif (! is_array($options[$name]))
				{
					$params[$name] = $options[$name];
				}
				else
				{
					$this->usageError("Option --$name requires a scalar. Array is given.");
				}
			}
			elseif ($name === 'args')
			{
				$params[$name] = $args;
			}
			elseif ($param->isDefaultValueAvailable())
			{
				$params[$name] = $param->getDefaultValue();
			}
			else
			{
				$this->usageError("Missing required option --$name.");
			}

			unset($options[$name]);
		}

		if (! empty($options))
		{
			$class = new \ReflectionClass(get_class($this));

			foreach ($options as $name => $value)
			{
				if ($class->hasProperty($name))
				{
					$property = $class->getProperty($name);

					if ($property->isPublic() && ! $property->isStatic())
					{
						$this->$name = $value;

						unset($options[$name]);
					}
				}
			}
		}

		if (! empty($options))
		{
			$this->usageError('Unknown options: ' . implode(', ',array_keys($options)));
		}

		$exitCode = -1;

		try
		{
			$this->_before($action, $params);

			$exitCode = $method->invokeArgs($this, $params);

			$exitCode = $this->_after($action, $params, $exitCode);
		}
		catch (CAssertException $e)
		{
			$this->_handleAssertException($e);
		}
		catch (\Throwable $e)
		{
			$this->_printError($e->getMessage());
		}

		$this->_displayErrors($action, $params, $exitCode);

		return $exitCode;
	}

	protected function _before($action, $params)
	{
	}

	protected function _after($action, $params, $exitCode = 0)
	{
		return $exitCode;
	}

	protected function _displayErrors($action, $params, $exitCode)
	{
	}

	protected function resolveRequest($args)
	{
		$options = $params = [];

		foreach ($args as $arg)
		{
			if (preg_match('|^--(\w+)(=(.*))?$|', $arg, $matches))
			{
				$name = $matches[1];

				$value = isset($matches[3]) ? $matches[3] : true;

				if (isset($options[$name]))
				{
					if (! is_array($options[$name]))
					{
						$options[$name] = [$options[$name]];
					}

					$options[$name][] = $value;
				}
				else
				{
					$options[$name] = $value;
				}
			}
			elseif (isset($action))
			{
				$params[] = $arg;
			}
			else
			{
				$action = $arg;
			}
		}

		if (! isset($action))
		{
			$action = $this->_defaultAction;
		}

		return [ $action, $options, $params ];
	}

	public function usageError($message)
	{
		echo "Error: $message\n\n" . $this->getHelp() . "\n";

		exit(1);
	}

	public function getHelp()
	{
		return '';
	}

	protected function _printError($error)
	{
		F::$mysql->rollbackAllTransactions(); /// если мы в транзакции, то ошибка не запишется в лог в бд

		$this->_error = $error;

		echo "\n$error\n";
	}

	protected function _handleAssertException(CAssertException $e)
	{
		F::$mysql->rollbackAllTransactions();

		list($file, $line, $message) = $e->getPlace();

		$error = "[" . date('Y-m-d H:i:s') . "] assert failed\nfile: $file:$line";

		if ($message !== '')
		{
			$error .= "\nmessage: $message";
		}

		$this->_error = $error;

		$this->_message = $message;

		echo "\n$error\n";
	}

	public function handleError($code, $message, $file, $line)
	{
		F::$mysql->rollbackAllTransactions();

		$error = "[" . date('Y-m-d H:i:s') . "] application error\nfile: $file:$line\nmessage: $message";

		$this->_error = $error;

		echo "\n$error\n";

		throw new \Exception($error); /// потому что иначе код продолжит выполняться с того же места, где произошла ошибка. а так мы уйдём в catch
	}

	public function addNotice($message)
	{
		$notice = "[" . date('Y-m-d H:i:s') . "] $message";

		$this->_notices[] = $notice;
	}

	protected function _assert($condition, $message = '')
	{
		if ((bool) $condition === false)
		{
			throw new CAssertException($message);
		}
	}

}
