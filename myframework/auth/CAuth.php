<?php
namespace myframework;

/**
 * Регистрация и авторизация
 */
abstract class CAuth extends CAuthShort
{
	/*
	 * Регистрация пользователя по логину и email
	 * Должен быть уникален логин и уникален email
	 * Соответственно, логиниться можно или по связке логин + пароль, или по email + пароль
	 */
	public function registerByLoginEmail(string $login, string $email, string $password, ?string $captcha): bool
	{
		if ($this->errors = $this->_validateCaptcha($captcha, 'register'))
		{
			return false;
		}

		if ($this->errors = $this->_validateLogin($login) + $this->_validateEmail($email) + $this->_validatePassword($password))
		{
			return false;
		}

		if ($this->loadUserByLogin($login))
		{
			$this->errors['login'] = $this->_messages['login.exists']; /// такой логин уже зарегистрирован

			return false;
		}

		if ($this->loadUserByEmail($email))
		{
			$this->errors['email'] = $this->_messages['email.exists_forgot']; /// такой email уже зарегистрирован

			return false;
		}

		$this->_registerNewUserLoginEmail($login, $email, $password);

		return true;
	}

	/*
	 * Регистрация пользователя по имени (фио) и email
	 * Должен быть уникален только email, а имя может быть неуникалным
	 * Соответственно, логиниться можно только по связке email + пароль
	 */
	public function registerByNameEmail(string $name, string $email, string $password, ?string $captcha): bool
	{
		if ($this->errors = $this->_validateCaptcha($captcha, 'register'))
		{
			return false;
		}

		if ($this->errors = $this->_validateName($name) + $this->_validateEmail($email) + $this->_validatePassword($password))
		{
			return false;
		}

		if ($this->loadUserByEmail($email))
		{
			$this->errors['email'] = $this->_messages['email.exists_forgot']; /// такой email уже зарегистрирован

			$this->_addCaptchaAction('register');

			return false;
		}

		$this->_registerNewUserNameEmail($name, $email, $password);

		$this->_addCaptchaAction('register');

		return true;
	}

	protected function _registerNewUserNameEmail(string $name, string $email, string $password): void
	{
		$transaction = F::$mysql->beginTransaction();

		$userId = $this->_saveUserNameEmailToDatabase($name, $email, $password);

		$token = $this->_createToken($userId, 'register');

		$transaction->commit();

		$this->_sendMailRegister($name, $email, $token);
	}

	protected function _registerNewUserLoginEmail(string $login, string $email, string $password): void
	{
		$transaction = F::$mysql->beginTransaction();

		$userId = $this->_saveUserLoginEmailToDatabase($login, $email, $password);

		$token = $this->_createToken($userId, 'register');

		$transaction->commit();

		$this->_sendMailRegister($login, $email, $token);
	}

	public function restore(string $email, ?string $captcha): bool
	{
		$email = remove_invisible_symbols($email);

		if ($this->errors = $this->_validateCaptcha($captcha, 'restore') or $this->errors = $this->_validateRestore($email))
		{
			return false;
		}

		$this->_restorePassword($email);

		return true;
	}

	protected function _validateRestore(string $email): array
	{
		$errors = $this->_validateEmail($email);

		if (! $errors)
		{
			if (! $this->loadUserByEmail($email))
			{
				$errors['email'] = $this->_messages['email.not_found']; /// такой email не зарегистрирован
			}
		}

		return $errors;
	}

	protected function _restorePassword(string $email): void
	{
		$user = $this->loadUserByEmail($email);

		$transaction = F::$mysql->beginTransaction();

		$token = $this->_createToken($user->id, 'restore');

		$this->_sendMailRestore($email, $token);

		$transaction->commit();
	}

	public function restoreConfirm(CModel $user, string $password, string $passwordRepeat): bool
	{
		if ($this->errors = $this->_validateRestoreConfirm($password, $passwordRepeat))
		{
			return false;
		}

		$this->_restoreConfirmComplete($user, $password);

		return true;
	}

	protected function _validateRestoreConfirm(string $password, string $passwordRepeat): array
	{
		$errors = $this->_validatePassword($password);

		if ($password != $passwordRepeat)
		{
			$errors['passwordRepeat'] = $this->_messages['password.not_match']; /// пароли не совпадают
		}

		return $errors;
	}

	protected function _restoreConfirmComplete(CModel $user, $password): void
	{
		$user->saveAttributes([
			'password' => $this->encodePassword($password),
		]);

		$this->loginUser($user);
	}

	protected function _sendMailRegister(string $name, string $email, string $token): void
	{
		/// отправка юзеру письма о регистрации
		/// определить в наследнике
	}

	protected function _sendMailRestore(string $email, string $token): void
	{
		/// отправка юзеру письма о восстановлении пароля
		/// определить в наследнике
	}

	/// создание нового юзера (имя + email + пароль)
	/// определить в наследнике
	abstract protected function _saveUserNameEmailToDatabase(string $name, string $email, string $password): int;

	/// создание нового юзера (логин + email + пароль)
	/// определить в наследнике
	abstract protected function _saveUserLoginEmailToDatabase(string $login, string $email, string $password): int;

	/// токен для подтверждения регистрации или восстановления пароля
	/// определить в наследнике
	abstract protected function _createToken(int $userId, string $action): string;

	public function createTokenHash(string $userId, string $action): string
	{
		return md5($userId . $action . $this->_tokenSalt);
	}

}
