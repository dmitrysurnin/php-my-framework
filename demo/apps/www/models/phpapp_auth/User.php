<?php
namespace www;

class User extends \super\User
{
	public static function loadByEmail(string $email): ?self
	{
		/** @var User $user */
		$user = F::$mysql->query(<<<sql
SELECT t.*
FROM phpapp_auth.users AS t
WHERE LOWER(t.email) = :lower_email
	AND t.email_confirmed = 1
LIMIT 1
sql
		)
			->param(':lower_email', strtolower($email))
			->fetchOneClass(User::class);

		return $user;
	}

	public static function loadByLogin(string $login): ?self
	{
		/** @var User $user */
		$user = F::$mysql->query(<<<sql
SELECT t.*
FROM phpapp_auth.users AS t
WHERE LOWER(t.login) = :lower_login
	AND t.email_confirmed = 1
LIMIT 1
sql
		)
			->param(':lower_login', strtolower($login))
			->fetchOneObject();

		return $user;
	}

	public static function createByNameEmail(string $name, string $email, string $password): int
	{
		/// создание нового юзера
		$ok = F::$mysql->query(<<<sql
INSERT INTO phpapp_auth.users SET
	name = :name,
	email = :email,
	password = :password
sql
		)
			->param(':name', $name) /// имя
			->param(':email', $email) /// здесь email не в lowercase, а как его ввёл юзер
			->param(':password', Auth::encodePassword($password)) /// хеш пароля
			->execute();

		$userId = F::$mysql->lastInsertId();

		return $userId;
	}

	public static function createByLoginEmail(string $login, string $email, string $password): int
	{
		/// создание нового юзера
		$ok = F::$mysql->query(<<<sql
INSERT INTO phpapp_auth.users SET
	login = :login,
	email = :email,
	password = :password
sql
		)
			->param(':login', $login) /// здесь login не в lowercase, а как его ввёл юзер
			->param(':email', $email) /// здесь email не в lowercase, а как его ввёл юзер
			->param(':password', Auth::encodePassword($password)) /// хеш пароля
			->execute();

		$userId = F::$mysql->lastInsertId();

		return $userId;
	}

}
