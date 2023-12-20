<?php
namespace admin;

class User extends \super\User
{
	public function relations(): array
	{
		return [
			'articles' => [Article::class, 'many', 'user_id'],
		];
	}

	public function validate(array &$post): array
	{
		$this->validNotEmpty($post, 'name');

		$this->validIsEmail($post, 'email');

		return $this->_errors;
	}

	protected function _beforeSave(array &$post)
	{
		$post['is_admin'] === '' and $post['is_admin'] = 0;
	}

	public function applyFilter(array $filter): self
	{
		$this->_applyFilterEqual($filter, 't.id');

		$this->_applyFilterLikeBoth($filter, 't.name');

		$this->_applyFilterLikeBoth($filter, 't.email');

		$this->_applyFilterEqual($filter, 't.is_admin');

		$this->_applyFilterEqual($filter, 't.email_confirmed');

		$this->_applyFilterGreater($filter, 'created_from', 't.created');

		$this->_applyFilterSmaller($filter, 'created_to', 't.created');

		$this->_applyFilterHavingEqual($filter, 'count_articles');

		return $this;
	}

}
