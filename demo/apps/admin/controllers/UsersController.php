<?php
namespace admin;

class UsersController extends Controller
{
	protected function _before()
	{
		parent::_before();

		$this->model = User::model();
	}

	public function actionIndex()
	{
		$this->model
			->select('t.*')
			->select('COUNT(articles.id) AS count_articles')
			->with(['articles'])
			->group('t.id');

		parent::actionIndex();
	}

	public function actionEdit($id = 0)
	{
		parent::actionEdit($id);
	}

}
