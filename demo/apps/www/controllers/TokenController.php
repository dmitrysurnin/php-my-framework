<?php
namespace www;

class TokenController extends Controller
{
	public function actionConfirm($hash)
	{
		$token = Token::loadByHash($hash);

		$ok = false;

		if ($token)
		{
			$ok = match ($token->action)
			{
				'register' => $token->register(),

				'restore' => $token->restore(),

				default => call_user_func_array([$token, $token->action], unserialize($token->data)),
			};
		}

		if ($ok)
		{
			$this->renderPage('confirm_' . $token->action, [
				'token' => $token,
			]);
		}
		else
		{
			$this->renderPage('confirm_failed');
		}
	}

}
