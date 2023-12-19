<?php
namespace www;

class Token extends \super\Token
{
	public static function createForAction(int $userId, string $action): string
	{
		$hash = md5($userId . $action . self::$_tokenSalt);

		F::$mysql->query(<<<sql
INSERT IGNORE INTO phpapp_auth.tokens SET
	user_id = :user_id,
	action = :action,
	hash = :hash
ON DUPLICATE KEY UPDATE
	last_sent = NOW()
sql
		)
			->param(':user_id', $userId)
			->param(':action', $action)
			->param(':hash', $hash)
			->execute();

		return $hash;

	}

	public static function loadByHash(string $hash): ?Token
	{
		/** @var Token $token */
		$token = F::$mysql->query(<<<sql
SELECT t.*
FROM phpapp_auth.tokens AS t
JOIN phpapp_auth.users AS u ON u.id = t.user_id
WHERE t.hash = :hash
	AND t.last_sent > DATE_SUB(NOW(), INTERVAL 14 DAY)
sql
		)
			->param(':hash', $hash)
			->fetchOneClass(Token::class);

		return $token;
	}

	public function register(): bool
	{
		$authInst = Auth::inst();

		/** @var User $user */
		$user = User::model()->findById($this->user_id);

		/// перед тем, как подтвердить регистрацию по токену, нужно проверить, занят ли этот email, т.к. за срок действия
		/// токена (несколько дней пока токен лежал в письме и ждал клика) уже кто-то мог занять этот email
		/// занятым считается только тот email, у которого стоит email_confirmed = 1
		$userFoundByEmail = $authInst->loadUserByEmail($user->email);

		if (! $userFoundByEmail)
		{
			$user->saveAttributes([
				'email_confirmed' => 1,
			]);

			$this->saveAttributes([
				'used' => 1,
			]);

			$authInst->loginUser($user);

			return true;
		}
		elseif ($userFoundByEmail->id == $user->id)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function restore(): bool
	{
		$this->saveAttributes([
			'used' => 1,
		]);

		F::$app->request->redirect('/auth/restoreconfirm/' . $this->hash);

		return true;
	}

}
