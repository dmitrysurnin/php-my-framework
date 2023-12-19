<?php
namespace myframework;

/*
 */
abstract class CHttpSessionHandler implements \SessionHandlerInterface
{
	public function open($path, $name): bool
	{
		return true;
	}

	public function read($id): string
	{
		return '';
	}

	public function write($id, $data): bool
	{
		return true;
	}

	public function destroy($id): bool
	{
		return true;
	}

	public function close(): bool
	{
		return true;
	}

	public function gc($max_lifetime): int
	{
		return true;
	}

}
