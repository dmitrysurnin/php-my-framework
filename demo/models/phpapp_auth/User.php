<?php
namespace super;

/**
 * @property integer $id
 * @property string $name
 * @property string $login
 * @property string $email
 * @property string $password
 * @property string $email_confirmed
 * @property int $is_admin
 * @property string $created
 * @property string $updated
 */
class User extends \myframework\CModel
{
	public function tableName(): string
	{
		return 'phpapp_auth.users';
	}

}
