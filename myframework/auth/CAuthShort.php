<?php
namespace myframework;

/**
 * Только авторизация, без регистрации
 */
abstract class CAuthShort extends CSingleton
{
	const MIN_LOGIN_LENGTH = 3; /// минимальная длина логина

	const MAX_LOGIN_LENGTH = 20; /// максимальная длина логина

	const MAX_NAME_LENGTH = 100; /// максимальная длина имени юзера

	const MAX_EMAIL_LENGTH = 100; /// максимальная длина email

	const MIN_PASSWORD_LENGTH = 6; /// минимальная длина пароля

	const MAX_PASSWORD_LENGTH = 64; /// максимальная длина пароля

	const CAPTCHA_LOGIN_COUNT = 5; /// показывать капчу после N попыток логина [в течение некоторого интервала времени]

	const CAPTCHA_REGISTER_COUNT = 5; /// показывать капчу после N регистраций [в течение некоторого интервала времени]

	public array $errors = [];

	protected CModel $_user;

	protected bool $_useCaptcha = true;

	protected array $_messages;

	protected string $_passwordSalt; /// определить в наследнике

	protected string $_tokenSalt; /// определить в наследнике

	protected function __construct()
	{
		$this->_messages = (require_once F_ROOT . '/messages/ru.php')['auth'];
	}

	public function loginByEmail(string $email, string $password, ?string $captcha, bool $remember = true): bool
	{
		if ($this->errors = $this->_validateCaptcha($captcha, 'login'))
		{
			return false;
		}

		if ($this->errors = $this->_validatePassword($password) + $this->_validateEmail($email))
		{
			return false;
		}

		if (! ($user = $this->loadUserByEmail($email))) /// не нашли в базе юзера с таким email
		{
			$this->errors['email'] = $this->_messages['email.not_found'];

			$this->_addCaptchaAction('login');

			return false;
		}
		elseif ($user->password != $this->encodePassword($password)) /// юзера нашли, но пароль не совпал
		{
			$this->errors['password'] = $this->_messages['password.wrong'];

			$this->_addCaptchaAction('login');

			return false;
		}

		$this->loginUser($user);

		return true;
	}

	public function loginUser($user)
	{
		$this->_user = $user;

		foreach ($user as $key => $value) /// будут добавлены только публичные поля, которые были выбраны в селекте
		{
			F::$app->session->{'user_' . $key} = $value;
		}
	}

	public function logout()
	{
		/// везде авторизация определяется исключительно по установленному user_id в сессии
		/// установка user_id (и других полей user_*) происходит в loginUser чуть выше
		F::$app->session->user_id = null;
	}

	abstract public function loadUserByEmail(string $email): ?CModel;

	abstract public function loadUserByLogin(string $login): ?CModel;

	protected function _validateName(string $name): array
	{
		$errors = [];

		if (! $name)
		{
			$errors['name'] = $this->_messages['name.empty'];
		}
		elseif (! preg_match('|^[а-яё \-]+$|ui', $name)) /// разрешены русские буквы, пробел и дефис. имя не уникально, потому без цифр и иных символов
		{
			$errors['name'] = $this->_messages['name.wrong_symbols'];
		}
		elseif (mb_strlen($name) > self::MAX_NAME_LENGTH)
		{
			$errors['name'] = $this->_message('name.too_long', ['{num}' => self::MAX_NAME_LENGTH]);
		}

		return $errors;
	}

	protected function _validateEmail(string $email): array
	{
		$errors = [];

		if (! $email)
		{
			$errors['email'] = $this->_messages['email.empty'];
		}
		elseif (! filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$errors['email'] = $this->_messages['email.wrong'];
		}
		elseif (mb_strlen($email) > self::MAX_EMAIL_LENGTH)
		{
			$errors['email'] = $this->_message('email.too_long', ['{num}' => self::MAX_EMAIL_LENGTH,]);
		}

		return $errors;
	}

	protected function _validatePassword(string $password): array
	{
		$errors = [];

		if (! $password)
		{
			$errors['password'] = $this->_messages['password.empty'];
		}
		elseif (($len = mb_strlen($password)) < self::MIN_PASSWORD_LENGTH)
		{
			$errors['password'] = $this->_message('password.too_short', ['{num}' => self::MIN_PASSWORD_LENGTH,]);
		}
		elseif ($len > self::MAX_PASSWORD_LENGTH)
		{
			$errors['password'] = $this->_message('password.too_long', ['{num}' => self::MAX_PASSWORD_LENGTH,]);
		}
		elseif (ctype_digit($password))
		{
			$errors['password'] = $this->_messages['password.only_digits'];
		}

		return $errors;
	}

	protected function _validateCaptcha(?string $captcha, string $action = ''): array
	{
		$errors = [];

		if ($this->_showCaptcha($action))
		{
			if (! $captcha)
			{
				$errors['captcha'] = $this->_messages['captcha.empty'];
			}
			elseif (! CCaptcha::checkCaptcha($captcha, $this->_captchaPrivateKey))
			{
				$errors['captcha'] = $this->_messages['captcha.wrong'];
			}

			if (! $errors) /// удачно введена капча
			{
				$this->_deleteAllCaptchaActions($action);
			}
		}

		return $errors;
	}

	protected function _validateLogin(string $login): array
	{
		$errors = [];

		if (! $login)
		{
			$errors['login'] = $this->_messages['login.empty'];
		}
		elseif (! preg_match('|^[\w\-]+$|', $login)) /// разрешены буквы, цифры, подчёркивание _ (входит в \w), минус
		{
			$errors['login'] = $this->_messages['login.wrong_symbols'];
		}
		elseif (($len = strlen($login)) < self::MIN_LOGIN_LENGTH)
		{
			$errors['login'] = $this->_message('login.too_short', ['{num}' => self::MIN_LOGIN_LENGTH]);
		}
		elseif ($len > self::MAX_LOGIN_LENGTH)
		{
			$errors['login'] = $this->_message('login.too_long', ['{num}' => self::MAX_LOGIN_LENGTH]);
		}

		return $errors;
	}

	protected function _showCaptcha(string $action): bool
	{
		if ($action == 'login')
		{
			return $this->_useCaptcha && (! self::CAPTCHA_LOGIN_COUNT || $this->_getCaptchaActionCount($action) >= self::CAPTCHA_LOGIN_COUNT);
		}
		elseif ($action == 'register')
		{
			return $this->_useCaptcha && (! self::CAPTCHA_REGISTER_COUNT || $this->_getCaptchaActionCount($action) >= self::CAPTCHA_REGISTER_COUNT);
		}

		return $this->_useCaptcha;
	}

	protected function _deleteAllCaptchaActions(string $action): void
	{
		/// обнулить счётчик попыток авотризации / регистрации
		/// определить в наследнике
	}

	protected function _addCaptchaAction(string $action): void
	{
		/// счётчик попыток авторизации либо регистрации с этого ip
		/// на каждую неудачную попытку для этого ip счётчик увеличивается на 1. при превышении некоторого порога вылазит капча
		/// определить в наследнике
	}

	protected function _getCaptchaActionCount(string $action): int
	{
		/// достать текущее значение счётчика
		/// определить в наследнике
		return 0;
	}

	public function encodePassword(string $password): string
	{
		return md5($password . $this->_passwordSalt);
	}

	protected function _message(string $key, array $params): string
	{
		$message = str_replace(array_keys($params), array_values($params), $this->_messages[$key]);

		return $message;
	}

}
