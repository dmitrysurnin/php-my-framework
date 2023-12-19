<?php
namespace super;

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property string $content
 * @property string $created
 * @property string $updated
 */
class Article extends \myframework\CModel
{
	public function tableName(): string
	{
		return 'phpapp.articles';
	}

}
