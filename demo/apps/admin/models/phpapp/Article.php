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

		$this->_applyFilterGreater($filter, 'created_from', 't.created');

		$this->_applyFilterSmaller($filter, 'created_to', 't.created');

		$this->_applyFilterGreater($filter, 'updated_from', 't.updated');

		$this->_applyFilterSmaller($filter, 'updated_to', 't.updated');

		return $this;
	}


}
