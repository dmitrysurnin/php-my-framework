<?php
namespace myframework;

/*
 * Тут создатели комманд к БД (т.е. строки SQL-запроса).
 */
trait CModelPrivateCommandCreators
{
	private function _createInsertCommand(array $attributes): void
	{
		$tableName = $this->tableName();

		$fields = [];

		foreach ($attributes as $name => $value)
		{
			if (is_int($name))
			{
				$fields[] = $value;
			}
			else
			{
				$key = ':' . uniqid();

				$fields[$name] = "`$name` = $key";

				$this->_modelData->_params[$key] = $value;
			}
		}

		$set = implode(', ', $fields);

		$this->_modelData->_sql = "INSERT INTO $tableName SET $set";
	}

	private function _createUpdateCommand(array $attributes): void
	{
		$tableName = $this->tableName();

		$fields = [];

		unset($attributes['id']);

		foreach ($attributes as $name => $value)
		{
			if (is_int($name))
			{
				$fields[] = $value;
			}
			else
			{
				$key = ':' . uniqid();

				$fields[$name] = "`$name` = $key";

				$this->_modelData->_params[$key] = $value;
			}
		}

		$set = implode(', ', $fields);

		$this->_modelData->_sql = "UPDATE $tableName SET $set WHERE id = :id";

		$this->_modelData->_params[':id'] = $this->id;

//		$this->_checkParams();
	}

	private function _createDeleteCommand(): void
	{
		$tableName = $this->tableName();

		$this->_modelData->_sql = "DELETE FROM $tableName WHERE id = :id";

		$this->_modelData->_params[':id'] = $this->id;
	}

	/*
	 * На данный момент не используется нигде. Единственное использование в insertUpdateByAttributes закрыто.
	 */
	private function _createInsertUpdateCommand(array $uniqueKey, array $attributesToUpdateOnDuplicateKey): void
	{
		$tableName = $this->tableName();

		$fieldsInsert = $fieldsUpdate = [];

		foreach ($uniqueKey as $name => $value)
		{
			if (is_int($name))
			{
				$fieldsInsert[] = $value;
			}
			else
			{
				$key = ':' . uniqid();

				$fieldsInsert[$name] = "`$name` = $key";

				$this->_modelData->_params[$key] = $value;
			}
		}

		foreach ($attributesToUpdateOnDuplicateKey as $name => $value)
		{
			if (is_int($name))
			{
				$fieldsInsert[] = $value;

				$fieldsUpdate[] = $value;
			}
			else
			{
				$key = ':' . uniqid();

				$fieldsInsert[$name] = "`$name` = $key";

				$fieldsUpdate[$name] = "`$name` = $key";

				$this->_modelData->_params[$key] = $value;
			}
		}

		$setInsert = implode(', ', $fieldsInsert);

		$setUpdate = implode(', ', $fieldsUpdate);

		$this->_modelData->_sql = "INSERT INTO $tableName SET $setInsert ON DUPLICATE KEY UPDATE $setUpdate, id = LAST_INSERT_ID(id)";
	}

	private function _createUpdateWhereCommand(array $attributesUpdate, array $attributesWhere = []): void
	{
		$tableName = $this->tableName();

		$fieldsUpdate = $fieldsWhere = [];

		unset($attributesUpdate['id']);

		foreach ($attributesUpdate as $name => $value)
		{
			if (is_int($name))
			{
				$fieldsUpdate[] = $value;
			}
			else
			{
				$key = ':' . uniqid();

				$fieldsUpdate[$name] = "`$name` = $key";

				$this->_modelData->_params[$key] = $value;
			}
		}

		foreach ($attributesWhere as $name => $value)
		{
			if (is_int($name))
			{
				$fieldsWhere[] = $value;
			}
			else
			{
				$key = ':' . uniqid();

				$fieldsWhere[$name] = "`$name` = $key";

				$this->_modelData->_params[$key] = $value;
			}
		}

		$setUpdate = implode(', ', $fieldsUpdate);

		$setWhere = implode(' AND ', $fieldsWhere);

		$this->_modelData->_sql = "UPDATE $tableName SET $setUpdate WHERE $setWhere";

		$this->_checkParams();
	}

	/*
	 * Необходимо убирать лишние неиспользуемые параметры, т.к. PDO падает, если параметров больше, чем нужно.
	 */
	private function _checkParams()
	{
		foreach ($this->_modelData->_params as $key => $param)
		{
			if (! str_contains($this->_modelData->_sql, $key))
			{
				unset($this->_modelData->_params[$key]);
			}
		}
	}

}
