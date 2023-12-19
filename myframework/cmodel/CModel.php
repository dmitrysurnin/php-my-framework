<?php
namespace myframework;

abstract class CModel extends \stdClass
{
	use CModelApplyFilter;

	use CModelPrivateCreateCommands;

	use CModelPrivateFetchers;

	use CModelPublicFinders;

	use CModelPublicUpdateDB;

	use CModelValidate;

	public static function model(): CModel
	{
		$model = new static();

		$model->_initModelData();

		return $model;
	}

	private function _initModelData()
	{
		$this->_modelData = (object) [
			'_select' => [],
			'_distinct' => false,
			'_with' => [],
			'_relations' => [],
			'_aliases' => [],
			'_join' => [],
			'_where' => [],
			'_params' => [],
			'_group' => '',
			'_having' => [],
			'_order' => '',
			'_limit' => '',
			'_offset' => '',
			'_calcFoundRows' => false,
			'_connector' => 'master',
		];
	}

	public function tableName(): string
	{
		throw new \Exception('нужно определить имя таблицы');
	}

	public function relations(): array
	{
		return [];
	}

	public function select(string $select): CModel
	{
		is_array($select) or $select = array_map('trim', explode(',', $select));

		$this->_modelData->_select = array_merge($this->_modelData->_select, $select);

		return $this;
	}

	public function distinct(): CModel
	{
		$this->_modelData->_distinct = true;

		return $this;
	}

	public function with(array|string $with, array $params = []): CModel
	{
		if (is_array($with))
		{
			F::assert($params === []);

			foreach ($with as $item)
			{
				$this->with($item);
			}
		}
		elseif (isset($this->_modelData->_with[$with]))
		{
			for ($n = 1; isset($this->_modelData->_with[$with . $n]); $n ++) ;

			$params['key'] = $with;

			$this->_modelData->_with[$with . $n] = $params;
		}
		else
		{
			if ($pos = stripos($with, ' AS '))
			{
				$params += ['as' => trim(substr($with, $pos + 4))];

				$with = substr($with, 0, $pos);

				$this->with($with, $params);
			}
			else
			{
				$this->_modelData->_with[$with] = $params;
			}
		}

		return $this;
	}

	public function join(string $join): CModel
	{
		$this->_modelData->_join[] = $join;

		return $this;
	}

	public function where(string $where, ?string $param = null): CModel
	{
		$key = ':' . uniqid();

		$this->_modelData->_where[$key] = $where;

		if ($param !== null)
		{
			if (str_contains($where, '?'))
			{
				$this->_modelData->_where[$key] = str_replace('?', $key, $this->_modelData->_where[$key]);
			}

			$this->_modelData->_params[$key] = $param;
		}

		return $this;
	}

	public function whereAttributes(array $where): CModel
	{
		foreach ($where as $name => $value)
		{
			if (is_int($name))
			{
				$this->where($value);
			}
			else
			{
				$this->compare($name, $value);
			}
		}

		return $this;
	}

	public function compare(string $name, ?string $param): CModel
	{
		if (is_array($param))
		{
			$this->_addInCondition($name, $param);
		}
		elseif ($param === null)
		{
			$this->where($name . ' IS NULL');
		}
		else
		{
			$this->where("$name = ?", $param);
		}

		return $this;
	}

	public function having(string $having, ?string $param = null): CModel
	{
		$key = ':' . uniqid();

		$this->_modelData->_having[$key] = $having;

		if ($param !== null)
		{
			if (str_contains($having, '?'))
			{
				$this->_modelData->_having[$key] = str_replace('?', $key, $this->_modelData->_having[$key]);
			}

			$this->_modelData->_params[$key] = $param;
		}

		return $this;
	}

	private function _addInCondition(string $columnName, array $values)
	{
		if ($values)
		{
			$params = [];

			$items = [];

			foreach ($values as $value)
			{
				$uid = uniqid();

				$params[":$uid"] = ":$uid";

				$items[":$uid"] = $value;
			}

			$this->_modelData->_where[] = $columnName . ' IN(' . implode(', ', $params) . ')';

			$this->_modelData->_params += $items;
		}
	}

	public function group($group): CModel
	{
		$this->_modelData->_group = $group;

		return $this;
	}

	public function order(string $order): CModel
	{
		$this->_modelData->_order = $order;

		return $this;
	}

	public function limit(int $limit): CModel
	{
		$this->_modelData->_limit = $limit;

		return $this;
	}

	public function offset(int $offset): CModel
	{
		$this->_modelData->_offset = $offset;

		return $this;
	}

	public function calcFoundRows(): CModel
	{
		$this->_modelData->_calcFoundRows = true;

		return $this;
	}

	public function getOrder(): string
	{
		return $this->_modelData->_order;
	}

	public function connector(string $name): CModel
	{
		$this->_modelData->_connector = $name;

		return $this;
	}

	public function getConnector(): string
	{
		return $this->_modelData->_connector;
	}

	private function _collectQuery(): string
	{
		$this->_collectWith();

		$this->_applySelect();

		$this->_applyWith();

		$this->_applyJoin();

		$this->_applyWhere();

		$this->_applyGroup();

		$this->_applyHaving();

		$this->_applyOrder();

		$this->_applyLimit();

		$this->_applyOffset();

		return $this->_modelData->_sql;
	}

	private function _collectWith(): void
	{
		$this->_modelData->_relations['t'] = (object) [
			'parent' => null,
			'children' => [],
			'key' => null,
			'alias' => 't',
			'model' => $this,
			'table_name' => $this->tableName(),
			'relations' => $this->relations(),
			'options' => ['as' => 't'],
		];

		$this->_modelData->_aliases['t'] = 't';

		foreach ($this->_modelData->_with as $with => $options)
		{
			$this->_addRelation($with, $options, 0);
		}
	}

	private function _addRelation(string $with, array $options, int $level)
	{
		if ($pos = strrpos($with, '.')) /// если $with содержит точку
		{
			$before = substr($with, 0, $pos); /// до последней точки

			$name = substr($with, $pos + 1); /// после последней точки

			$this->_addRelation($before, [], $level + 1);
		}
		else
		{
			$before = 't';

			$name = $with;
		}

		if (! isset($this->_modelData->_relations[$with]))
		{
			$o = (! $level ? $options : []) + ['as' => $name];

			$alias = &$o['as'];

			if (isset($this->_modelData->_aliases[$alias])) /// если алиас уже кем-то занят, добавить цифру в конец
			{
				for ($i = 2; isset($this->_modelData->_aliases[$alias . $i]); ++ $i) ;

				$alias = $alias . $i;
			}

			$key = $options['key'] ?? $name;

			$relationClassName = $this->_modelData->_relations[$before]->relations[$key][0];

			/** @var CModel $relationClassName */
			$relationModel = $relationClassName::model();

			$this->_modelData->_relations[$with] = (object) [
				'parent' => $before,
				'children' => [],
				'key' => $name,
				'alias' => $alias,
				'model' => $relationModel,
				'table_name' => $relationModel->tableName(),
				'relations' => $relationModel->relations(),
				'options' => $o,
			];

			$this->_modelData->_relations[$before]->children[$with] = &$this->_modelData->_relations[$with];

			$this->_modelData->_aliases[$alias] = $with;
		}
	}

	private function _applySelect(): void
	{
		$this->_modelData->_select === [] and $this->_modelData->_select = ['t.*'];

		$this->_modelData->_sql = 'SELECT ';

		$this->_modelData->_distinct and $this->_modelData->_sql .= 'DISTINCT ';

		$this->_modelData->_sql .= '/*+ MAX_EXECUTION_TIME(30000) */ ';

		if ($this->_modelData->_calcFoundRows)
		{
			$this->_modelData->_sql .= 'SQL_CALC_FOUND_ROWS ';
		}

		$this->_modelData->_sql .= implode(', ', $this->_modelData->_select);

		$this->_modelData->_sql .= ' FROM ' . $this->tableName() . ' AS `t`';
	}

	private function _applyWith(): void
	{
		/** @var \stdClass $relation */
		foreach ($this->_modelData->_relations as $relationPath => $relation)
		{
			if ($relationPath != 't')
			{
				$parent = $relation->parent;

				$relationKey = $relation->options['key'] ?? $relation->key;

				$array = $this->_modelData->_relations[$parent]->relations[$relationKey];

				list($className, $type, $keyField) = array_pad($array, 3, null);

				$type === null and $type = 'one';

				$keyField === null and $keyField = $relationKey . '_id';

				$parentAlias = $this->_modelData->_relations[$parent]->alias;

				$relationTable = $relation->table_name;

				$relationAlias = $relation->alias;

				$joinType = 'LEFT JOIN'; /// если $type == 'one', то всё равно берём не JOIN, а LEFT JOIN, т.к. JOIN создаёт дополнительную проверку на наличие строки и таким образом может замедлять запрос

				$this->_modelData->_sql .= " $joinType $relationTable AS `$relationAlias`";

				if (isset($relation->options['on']))
				{
					$this->_modelData->_sql .= ' ON ' . $relation->options['on'];
				}
				elseif ($type == 'one')
				{
					$this->_modelData->_sql .= " ON `$parentAlias`.`$keyField` = `$relationAlias`.`id`";
				}
				else
				{
					$this->_modelData->_sql .= " ON `$parentAlias`.`id` = `$relationAlias`.`$keyField`";
				}

				if (isset($relation->options['and_on']))
				{
					$this->_modelData->_sql .= " AND " . $relation->options['and_on'];
				}
			}
		}
	}

	private function _applyJoin(): void
	{
		foreach ($this->_modelData->_join AS $join)
		{
			$this->_modelData->_sql .= ' ' . $join;
		}
	}

	private function _applyWhere(): void
	{
		if ($this->_modelData->_where)
		{
			$this->_modelData->_sql .= ' WHERE ' . implode(' AND ', $this->_modelData->_where);
		}
	}

	private function _applyGroup(): void
	{
		if ($this->_modelData->_group)
		{
			$this->_modelData->_sql .= ' GROUP BY ' . $this->_modelData->_group;
		}
	}

	private function _applyHaving(): void
	{
		if ($this->_modelData->_having)
		{
			$this->_modelData->_sql .= ' HAVING ' . implode(' AND ', $this->_modelData->_having);
		}
	}

	private function _applyOrder(): void
	{
		if ($this->_modelData->_order)
		{
			$this->_modelData->_sql .= ' ORDER BY ' . $this->_modelData->_order;
		}
	}

	private function _applyLimit(): void
	{
		if ($this->_modelData->_limit >= 0)
		{
			$this->_modelData->_sql .= ' LIMIT ' . $this->_modelData->_limit;
		}
	}

	private function _applyOffset(): void
	{
		if ($this->_modelData->_offset > 0)
		{
			$this->_modelData->_sql .= ' OFFSET ' . $this->_modelData->_offset;
		}
	}

}
