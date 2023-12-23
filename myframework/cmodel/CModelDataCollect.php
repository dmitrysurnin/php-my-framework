<?php
namespace myframework;

/*
 * Тут собираем SQL-запрос на основе объекта _modelData.
 */
trait CModelDataCollect
{
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
