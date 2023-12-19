<?php
namespace super;

/**
 * @property integer $id
 * @property string $uid
 * @property string $data
 * @property string $created
 * @property string $updated
 */
class Session extends \myframework\CModel
{
	public function tableName(): string
	{
		return 'phpapp_auth.sessions';
	}

	public static function loadBySid(string $sid): ?self
	{
		/** @var Session $session */
		$session = F::$mysql->query(<<<sql
SELECT t.id, t.data
FROM phpapp_auth.sessions AS t
WHERE t.sid = :sid
sql
		)
			->param(':sid', $sid)
			->fetchOneClass(self::class);

		return $session;
	}

	public static function createEmptySid(string $sid): string
	{
		F::$mysql->query(<<<sql
INSERT INTO phpapp_auth.sessions SET
	sid = :sid
sql
		)
			->param(':sid', $sid)
			->execute();

		return F::$mysql->lastInsertId();
	}

	public static function updateDataBySid(string $sid, string $data): bool
	{
		$ok = F::$mysql->query(<<<sql
UPDATE phpapp_auth.sessions SET
	data = :data
WHERE sid = :sid
sql
		)
			->param(':sid', $sid)
			->param(':data', $data)
			->execute();

		return !! $ok;
	}

}
