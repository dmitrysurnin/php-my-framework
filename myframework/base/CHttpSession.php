<?php
namespace myframework;

/**
 * @property int $user_id - доступно с magic-метод __get()
 * @property int $user_email - доступно с magic-метод __get()
 * ...
 * заполняются в loginUser()
 */
abstract class CHttpSession extends CSingleton
{
	public ?string $sid = null; /// id текущей сессии пользователя

	abstract public function start();

	public function __set(string $name, $value)
	{
		$_SESSION[$name] = $value;
	}

	public function __get(string $name)
	{
		return $_SESSION[$name] ?? null;
	}

}
