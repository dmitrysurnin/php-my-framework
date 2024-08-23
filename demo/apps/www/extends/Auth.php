<?php
namespace www;

final class Auth extends \myframework\CAuth
{
	protected string $_passwordSalt = '12345678901234567890123456789012'; /// todo: изменить на свой случайный набор символов

	protected string $_tokenSalt = '12345678901234567890123456789012'; /// todo: изменить на свой случайный набор символов

	protected string $_captchaPrivateKey = '6LdSqTApAAAAAL7dIk0h7y0eLNEOOWHf_ITpv3Lw'; /// todo: изменить ключ на свой

	protected function _getCaptchaActionCount(string $action): int
	{
		return CaptchaAction::getCount($action);
	}

	public function loadUserByEmail(string $email): ?User
	{
		return User::loadByEmail($email);
	}

	public function loadUserByLogin(string $login): ?User
	{
		return User::loadByLogin($login);
	}

	protected function _saveUserLoginEmailToDatabase(string $login, string $email, string $password): int
	{
		return User::createByLoginEmail($login, $email, $password);
	}

	protected function _saveUserNameEmailToDatabase(string $name, string $email, string $password): int
	{
		return User::createByNameEmail($name, $email, $password);
	}

	protected function _createToken(int $userId, string $action): string
	{
		return Token::createForAction($userId, $action);
	}

	protected function _sendMailRegister(string $name, string $email, string $token): void
	{
		$mailer = new Mailer();

		$mailer->sendRegister($name, $email, $token);
	}

	protected function _sendMailRestore(string $email, string $token): void
	{
		$mailer = new Mailer();

		$mailer->sendRestore($email, $token);
	}

}
