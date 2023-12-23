<?php
namespace myframework;

/*
 * Объект _modelData содержит все данные, необходимые для построения SQL-запроса.
 * Тут заполняем его.
 */
trait CModelDataBuild
{
	public function init()
	{
		$this->_initModelData();

		$this->id = 0;
	}

	private function _initModelData()
	{
		/// поля, которые используются при постоении SQL-запроса
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
			$this->inArray($name, $param);
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

	public function inArray(string $columnName, array $values)
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

	public function connector(string $name): CModel
	{
		$this->_modelData->_connector = $name;

		return $this;
	}

	public function getOrder(): string
	{
		return $this->_modelData->_order;
	}

	public function getConnector(): string
	{
		return $this->_modelData->_connector;
	}

}
