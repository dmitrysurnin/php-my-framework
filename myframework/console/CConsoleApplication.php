<?php
namespace myframework;

class CConsoleApplication extends CApplication
{
	private $_runner;

	private $_commandPath;

	public function __construct(array $config)
	{
		F::$app = $this;

		parent::__construct($config);

		$this->_runner = new CConsoleCommandRunner();
	}

	protected function _processRequest()
	{
		$exitCode = $this->_runner->run($_SERVER['argv']);

		$this->end($exitCode);
	}

	public function getCommandPath()
	{
		$this->_commandPath = ROOT . '/apps/console/commands';

		return $this->_commandPath;
	}

}
