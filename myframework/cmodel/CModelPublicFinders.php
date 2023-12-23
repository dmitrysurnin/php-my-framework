<?php
namespace myframework;

/*
 * Тут публичные методы на выборку (извлечение) данных.
 */
trait CModelPublicFinders
{
	public function find(): ?static
	{
		F::assert(empty($this->id));

		$this->limit(1);

		$result = $this->_fetchOneIntoModel();

		unset($this->_modelData);

		return $result;
	}

	public function findById($id): ?static
	{
		F::assert(empty($this->id));

		$this->where('t.id = ?', $id);

		$result = $this->_fetchOneIntoModel();

		unset($this->_modelData);

		return $result;
	}

	public function findByAttributes(array $attributes = []): ?static
	{
		F::assert(empty($this->id));

		$this->whereAttributes($attributes);

		$this->limit(1);

		$result = $this->_fetchOneIntoModel();

		unset($this->_modelData);

		return $result;
	}

	public function findAll(): array
	{
		F::assert(empty($this->id));

		$result = $this->_fetchAllClass();

		return $result;
	}

	public function findDistinctValues(string $field): array
	{
		F::assert(empty($this->id));

		$this->select("t.$field");

		$this->distinct();

		$this->order("t.$field");

		$result = $this->_fetchAllArrayPlain();

		return $result;
	}

	public function findApproximateCount(): int
	{
		$tableName = $this->tableName();

		$result = F::$mysql->query(<<<sql
SELECT t.TABLE_ROWS AS table_rows
FROM information_schema.TABLES AS t
WHERE CONCAT(t.TABLE_SCHEMA, '.', t.TABLE_NAME) = :table_name
sql
		)
			->param(':table_name', $tableName)
			->fetchOneArray()
			['table_rows'];

		return $result;
	}

}
