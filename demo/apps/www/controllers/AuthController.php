<?php
namespace www;

class AuthController extends Controller
{
	private Auth $_auth;

	protected function _before()
	{
		parent::_before();

		$this->_auth = Auth::inst();

		$this->addCss('pages/auth.css');
	}

	/*
	 * /auth
	 */
	public function actionIndex($email = '', $password = '')
	{
		$this->title = 'Авторизация';

		if ($this->session->user_id)
		{
			$this->redirect();
		}

		if ($_POST)
		{
			if ($this->_auth->loginByEmail($email, $password, $this->request->post('g-recaptcha-response')))
			{
				$this->redirect();
			}
		}

		$this->renderPage('login', [
			'errors' => $this->_auth->errors,
		]);
	}

	/*
	 * /auth/register
	 */
	public function actionRegister($name = '', $email = '', $password = '')
	{
		$this->title = 'Регистрация';

		if ($this->session->user_id)
		{
			$this->redirect();
		}

		if ($_POST)
		{
			if ($this->_auth->registerByNameEmail($name, $email, $password, $this->request->post('g-recaptcha-response')))
			{
				$this->renderPage('register_success', [
					'email' => $email,
				]);

				return;
			}
		}

		$this->renderPage('register', [
			'errors' => $this->_auth->errors,
		]);
	}

	/*
	 * /auth/restore
	 */
	public function actionRestore($email = '')
	{
		$this->title = 'Восстановление пароля';

		if ($this->session->user_id)
		{
			$this->redirect();
		}

		if ($_POST)
		{
			if ($this->_auth->restore($email, $this->request->post('g-recaptcha-response')))
			{
				$this->renderPage('restore_success', [
					'email' => $email,
				]);

				return;
			}
		}

		$this->renderPage('restore', [
			'errors' => $this->_auth->errors,
		]);
	}

	public function actionRestoreConfirm($hash, $password = null, $passwordRepeat = null)
	{
		$this->title = 'Восстановление пароля';

		if ($this->session->user_id)
		{
			$this->redirect();
		}

		$token = Token::loadByHash($hash);

		if (! $token)
		{
			$this->renderPage('restore_confirm_failed');

			return;
		}

		if ($_POST)
		{
			/** @var User $user */
			$user = User::model()->findById($token->user_id);

			if ($this->_auth->restoreConfirm($user, $password, $passwordRepeat))
			{
				$this->renderData('restore_confirm_success');

				return;
			}
		}

		$this->renderData('restore_confirm', [
			'token' => $token,
			'errors' => $this->_auth->errors,
		]);
	}

	public function actionLogout()
	{
		$this->_auth->logout();

		$this->redirect();
	}

}
