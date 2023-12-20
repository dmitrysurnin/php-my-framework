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

		$this->validNotEmpty($post, 'html');

		return $this->_errors;
	}

	protected function _beforeSave(array &$post)
	{
	}

	public function applyFilter(array $filter): self
	{
		$this->_applyFilterEqual($filter, 't.id');

		$this->_applyFilterEqual($filter, 't.user_id');

		$this->_applyFilterLikeBoth($filter, 't.title');

		return $this;
	}


}
