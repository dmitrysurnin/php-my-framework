<?php
namespace myframework;

/*
 * Тут методы на изменение (создание, обновление, удаление) данных.
 */
trait CModelPublicUpdateDB
{
	public function save(array $post = []): bool
	{
		$postOriginal = $post;

		$transaction = F::$mysql->beginTransaction();

		$this->_beforeSave($post); /// тут можно внести изменения в $post = $postOriginal перед записью в бд

		if (empty($this->id))
		{
			$ok = !! $this->insert($post);
		}
		else
		{
			$ok = !! $this->update($post);
		}

		$this->_afterSave($postOriginal);

		$transaction->commit();

		return $ok;
	}

	public function insert(array $post = []): ?CModel
	{
		F::assert(empty($this->id)); /// нельзя вызвать "insert" для уже найденной модели, можно только для пустой (не инициализированной)

		$this->_createInsertCommand($post);

		if ($this->_execute())
		{
			$this->id = F::$mysql->lastInsertId();

			$this->setAttributes($post); /// надо ли это тут?

			return $this;
		}

		return null;
	}

	public function update(array $post = []): ?CModel
	{
		F::assert(! empty($this->id)); /// нельзя вызвать "update" у пустой модели, сначала выполните "find"

		$this->_initModelData(); /// требуется, т.к модель уже заполнена, значит не инициализирована

		$this->_createUpdateCommand($post);

		if ($this->_execute())
		{
			$this->setAttributes($post); /// надо ли это тут?

			return $this;
		}

		return null;
	}

	public function updateById(int $id, array $post): ?CModel
	{
		F::assert(empty($this->id));

		$this->id = $id;

		return $this->update($post);
	}

	public function delete(): int
	{
		F::assert(! empty($this->id));

		$this->_initModelData(); /// требуется, т.к модель уже заполнена, значит не инициализирована

		$this->_createDeleteCommand();

		$result = $this->_execute();

		return $result;
	}

	public function deleteById(int $id): int
	{
		F::assert(empty($this->id));

		$this->id = $id;

		return $this->delete();
	}

	public function saveAttributes(array $post = []): ?CModel
	{
		return $this->update($post);
	}

	public function saveAttributesById(int $id, array $post = []): ?CModel
	{
		return $this->updateById($id, $post);
	}

	public function createByAttributes(array $post): ?CModel
	{
		return $this->insert($post);
	}

	/**
	 * Метод, который перед созданием ищет, есть ли уже такая строка, и если есть, то возвращает её.
	 */
	public function findCreateByAttributes(array $attributesFind, array $attributesCreate = []): CModel
	{
		F::assert(empty($this->id));

		if (($found = $this->findByAttributes($attributesFind)) !== null)
		{
			return $found;
		}

		$this->_initModelData(); /// требуется, т.к чуть выше был find

		$this->insert($attributesFind + $attributesCreate); /// INSERT INTO ... SET ...

		return $this;
	}

	/**
	 * Ранее было:
	 * INSERT INTO ... SET ($attributesToInsert) ON DUPLICATE KEY UPDATE ($attributesToUpdateOnDuplicateKey)
	 * но впоследствии было сделано через find + отдельные insert либо update, т.к. on duplicate key увеличивает auto_increment при update
	 * https://stackoverflow.com/questions/23516958/on-duplicate-key-auto-increment-issue-mysql
	 */
	public function insertUpdateByAttributes(array $uniqueKey, array $attributesToUpdateOnDuplicateKey): CModel
	{
		F::assert(empty($this->id));

		if (($found = $this->findByAttributes($uniqueKey)) !== null) /// найдено, нужно update
		{
			$found->update($attributesToUpdateOnDuplicateKey);

			return $found;
		}
		else /// не найдено, нужно insert
		{
			$this->_initModelData(); /// требуется, т.к был find чуть выше

			$this->insert($uniqueKey + $attributesToUpdateOnDuplicateKey);

			return $this;
		}

		$this->_createInsertUpdateCommand($uniqueKey, $attributesToUpdateOnDuplicateKey);

		$this->_execute();

		$this->id = F::$mysql->lastInsertId();

		$this->setAttributes($uniqueKey + $attributesToUpdateOnDuplicateKey); /// надо ли это тут?

		return $this;
	}

	public function updateWhere(array $attributesUpdate, array $attributesWhere): int
	{
		F::assert(empty($this->id));

		$this->_createUpdateWhereCommand($attributesUpdate, $attributesWhere);

		return $this->_execute();
	}

	private function _execute(): int
	{
		$query = new CMysqlQuery($this->_modelData->_sql);

		$query->params($this->_modelData->_params);

		$rows = $query->execute();

		unset($this->_modelData);

		return $rows;
	}

	public function setAttributes(array $attributes = []): CModel
	{
		foreach ($attributes as $name => $value)
		{
			if (! is_int($name))
			{
				$this->$name = $value;
			}
		}

		return $this;
	}

	public function setAttributesFromSelect(): CModel
	{
		$select = $this->_modelData->_select;

		$select === [] and $select[] = 't.*';

		foreach ($select as $value)
		{
			if ($pos = strpos($value, ' AS '))
			{
				$name = trim(substr($value, $pos + 4));

				$this->$name = '';
			}
			elseif (str_starts_with($value, 't.'))
			{
				$name = substr($value, 2);

				if ($name == '*')
				{
					$columns = $this->_getTableColumns();

					foreach ($columns as $column)
					{
						$this->$column = '';
					}
				}
				else
				{
					$this->$name = '';
				}
			}
		}

		return $this;
	}

	private function _getTableColumns(): array
	{
		$tableName = $this->tableName();

		$data = F::$mysql->query("DESCRIBE $tableName")->fetchAllArray();

		$array = [];

		foreach ($data as $item)
		{
			$array[] = $item['Field'];
		}

		return $array;
	}

	protected function _beforeSave(array &$post)
	{
	}

	protected function _afterSave(array &$post)
	{
	}

}
