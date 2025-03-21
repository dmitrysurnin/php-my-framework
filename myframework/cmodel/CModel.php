<?php
namespace myframework;

abstract class CModel extends \stdClass
{
	use CModelApplyFilters;

	use CModelDataBuild;

	use CModelDataCollect;

	use CModelPrivateCommandCreators;

	use CModelPrivateFetchers;

	use CModelPublicFinders;

	use CModelPublicUpdateDB;

	use CModelValidate;

	public static function model(): static
	{
		$model = new static();

		$model->init();

		return $model;
	}

	public function tableName(): string
	{
		throw new \Exception('нужно определить имя таблицы');
	}

	public function relations(): array
	{
		return [];
	}

}
