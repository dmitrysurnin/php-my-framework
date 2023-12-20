<?php
namespace admin;

/**
 * @property Article $model
 */
class ArticlesController extends Controller
{
	protected function _before()
	{
		parent::_before();

		$this->model = Article::model();
	}

	public function actionIndex()
	{
		$this->model
			->select('t.id, t.user_id, t.title, t.content, t.created, t.updated')
			->select('user.name AS user_name')
			->with(['user']);

		parent::actionIndex();
	}

	public function actionEdit($id = 0)
	{
		parent::actionEdit($id);
	}

}
