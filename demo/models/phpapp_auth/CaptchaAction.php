<?php
namespace super;

/**
 * @property integer $id
 * @property string $action
 * @property string $created
 */
class CaptchaAction extends \myframework\CModel
{
	public function tableName(): string
	{
		return 'phpapp_auth.captcha_actions';
	}

	public static function getCount(string $action): int
	{
		return 0; /// todo: достать значение счётчика из бд
	}

}
