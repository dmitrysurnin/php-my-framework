<?php
namespace super;

/**
 * @property int $id
 * @property int $user_id
 * @property string $action
 * @property string $used
 * @property string $hash
 * @property string $last_sent
 * @property string $created
 */
class Token extends \myframework\CModel
{
	public function tableName(): string
	{
		return 'phpapp_auth.tokens';
	}

	public function relations(): array
	{
		return [
			'user' => [User::class],
		];
	}

	public function applyFilter(array $filter): self
	{
		return $this;
	}

}
