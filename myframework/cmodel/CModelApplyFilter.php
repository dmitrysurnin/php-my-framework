<?php
namespace myframework;

trait CModelApplyFilter
{
	public function applyFilter(array $filter): self
	{
		$this->_applyFilterEqual($filter, 't.id');

		$this->_applyFilterLike($filter, 't.name');

		$this->_applyFilterEqual($filter, 't.hidden');

		return $this;
	}

	protected function _applyFilterEqual(array $filter, string $name, string $field = null): void
	{
		$field or $field = $name;

		if (isset($filter[$name]) && $filter[$name] !== '')
		{
			$this->where("$field = ?", $filter[$name]);
		}
	}

	protected function _applyFilterNotEmpty(array $filter, string $name, string $field = null): void
	{
		$field or $field = $name;

		if (isset($filter[$name]) && $filter[$name] !== '')
		{
			$this->where("$field IS " . ($filter[$name] ? 'NOT ' : '') . 'NULL');
		}
	}

	protected function _applyFilterLike(array $filter, string $name, string $field = null): void
	{
		$field or $field = $name;

		if (isset($filter[$name]) && $filter[$name] !== '')
		{
			$this->where($field . (isset($filter[$name . '.not']) ? ' NOT' : '') . ' LIKE ?', str_replace('_', '\_', $filter[$name]) . '%');
		}
	}

	protected function _applyFilterLikeBoth(array $filter, string $name, string $field = null): void
	{
		$field or $field = $name;

		if (isset($filter[$name]) && $filter[$name] !== '')
		{
			$this->where($field . (isset($filter[$name . '.not']) ? ' NOT' : '') . ' LIKE ?', '%' . str_replace('_', '\_', $filter[$name]) . '%');
		}
	}

	public function _applyFilterGreater(array $filter, string $name, string $field = null): void
	{
		$field or $field = $name;

		if (isset($filter[$name]) && $filter[$name] !== '')
		{
			$this->where("$field >= ?", $filter[$name]);
		}
	}

	public function _applyFilterSmaller(array $filter, string $name, string $field = null): void
	{
		$field or $field = $name;

		if (isset($filter[$name]) && $filter[$name] !== '')
		{
			$this->where("$field <= ?", $filter[$name]);
		}
	}

	protected function _applyFilterEqualOrEmpty(array $filter, string $name, string $field = null): void
	{
		$field or $field = $name;

		if (! empty($filter[$name]) && $filter[$name] !== '-1')
		{
			$this->where("$field = ?", $filter[$name]);
		}
		elseif (! empty($filter[$name]) && $filter[$name] === '-1')
		{
			$this->where("$field != ''");
		}
		elseif (isset($filter[$name]) && $filter[$name] === '0')
		{
			$this->where("$field = ''");
		}
	}

	public function _applyFilterHavingLikeBoth(array $filter, string $name, string $field = null): void
	{
		$field or $field = $name;

		if (isset($filter[$name]) && $filter[$name] !== '')
		{
			$this->having("$field LIKE ?", '%' . $filter[$name] . '%');
		}
	}

	public function _applyFilterHavingEqual(array $filter, string $name, string $field = null): void
	{
		$field or $field = $name;

		if (isset($filter[$name]) && $filter[$name] !== '')
		{
			$this->having("$field = ?", $filter[$name]);
		}
	}

	public function _applyFilterHavingNotEmpty(array $filter, string $name, string $field = null): void
	{
		$field or $field = $name;

		if (isset($filter[$name]) && $filter[$name] !== '')
		{
			$this->having("$field IS " . ($filter[$name] ? 'NOT ' : '') . 'NULL');
		}
	}

	public function _applyFilterHavingGreater(array $filter, string $name, string $field = null): void
	{
		$field or $field = $name;

		if (isset($filter[$name]) && $filter[$name] !== '')
		{
			$this->having("$field >= ?", $filter[$name]);
		}
	}

	public function _applyFilterHavingSmaller(array $filter, string $name, string $field = null): void
	{
		$field or $field = $name;

		if (isset($filter[$name]) && $filter[$name] !== '')
		{
			$this->having("$field <= ?", $filter[$name]);
		}
	}

}
