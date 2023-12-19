<?php
namespace myframework;

class CConsoleCommandRunner // extends CComponent
{
	public function run($args)
	{
		$file = $args[1];

		$args = array_slice($args, 2);

		$command = $this->createCommand($file);

		$command->init();

		$exitCode = $command->run($args);

		return $exitCode;
	}

	public function createCommand(string $file): CConsoleCommand
	{
		$pos = strrpos($file, '/');

		$directory = substr($file, 0, $pos);

		$baseName = substr($file, $pos + 1);

		$baseName = ucfirst($baseName) . 'Command';

		$className = PROJECT . '\\' . $baseName;

		$fileName = APP . "/$directory/$baseName.php";

		require_once $fileName;

		return new $className($file, $this);
	}

}
