<?php
namespace admin;

class Article extends \super\Article
{
	public function relations(): array
	{
		return [
			'user' => [User::class],
		];
	}

	public function validate(array &$post): array
	{
		$this->validNotEmpty($post, 'title');

		$this->validNotEmpty($post, 'content');

		return $this->_errors;
	}

	protected function _beforeSave(array &$post)
	{
		! $this->id and $post['user_id'] = F::$app->session->user_id;
	}

	public function applyFilter(array $filter): self
	{
		$this->_applyFilterEqual($filter, 't.id');

		$this->_applyFilterEqual($filter, 't.user_id');

		$this->_applyFilterLikeBoth($filter, 't.title');

		return $this;
	}


}
