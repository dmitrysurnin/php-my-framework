<?php
namespace myframework;

/**
 */
class CMysqlQuery
{
	protected string $_sql;

	protected array $_params = [];

	protected string $_connector = 'master';

	public function __construct(string $sql)
	{
		$this->_sql = $sql;
	}

	public function param(string $key, ?string $value): CMysqlQuery
	{
		$this->_params[$key] = $value;

		return $this;
	}

	public function params(array $array): CMysqlQuery
	{
		$this->_params = $array;

		return $this;
	}

	public function connector(string $name): CMysqlQuery
	{
		$this->_connector = $name;

		return $this;
	}

	public function fetchOneArray(): ?array
	{
		return $this->_fetch(true, \PDO::FETCH_ASSOC);
	}

	public function fetchOneArrayNum(): array
	{
		return $this->_fetch(true, \PDO::FETCH_NUM);
	}

	public function fetchOneObject(): ?\stdClass
	{
		$result = $this->_fetch(true, \PDO::FETCH_OBJ);

		return $result ?: null;
	}

	public function fetchOneClass(string $className): ?CModel
	{
		return $this->_fetch(true, \PDO::FETCH_CLASS, $className);
	}

	public function fetchOneIntoModel(CModel $model): CModel|null
	{
		return $this->_fetch(true, \PDO::FETCH_INTO, $model);
	}

	public function fetchAllArray(): array
	{
		return $this->_fetch(false, \PDO::FETCH_ASSOC);
	}

	public function fetchAllObject(): array
	{
		return $this->_fetch(false, \PDO::FETCH_OBJ);
	}

	public function fetchAllArrayNum(): array
	{
		return $this->_fetch(false, \PDO::FETCH_NUM);
	}

	public function fetchAllArrayPlain(): array
	{
		return $this->fetchAllColumn(0);
	}

	public function fetchAllColumn(int $col): array
	{
		return $this->_fetch(false, \PDO::FETCH_COLUMN, $col);
	}

	/**
	 * @return CModel[]
	 */
	public function fetchAllClass(string $className): array
	{
		$array = $this->_fetch(false, \PDO::FETCH_CLASS, $className);

		if ($array && isset($array[0]->id)) /// если в выборке присутствует колонка с именем id, то считаем, что это уникальный ключ, и сделаем его одновременно ключом массива
		{
			$result = [];

			foreach ($array as $value)
			{
				$result[$value->id] = $value;
			}

			return $result;
		}

		return $array;
	}

	/**
	 * @return CModel[]
	 */
	public function fetchAllClassPlain(string $className): array
	{
		$array = $this->_fetch(false, \PDO::FETCH_CLASS, $className);

		return $array;
	}

	/**
	 * @return CModel[]
	 */
	public function fetchAllKeyPair(): array
	{
		return $this->_fetch(false, \PDO::FETCH_KEY_PAIR);
	}

	protected function _fetch($one = false, $fetchStyle = null, $fetchArgument = null)
	{
		$this->_checkParams(); /// pdo выдаст ошибку, если переданы лишние параметры, потому уберём их, если они есть

		$connector = F::$mysql->getConnector($this->_connector);

		$pdo = $connector->pdo;

		$statement = $pdo->prepare($this->_sql);

		if ($one)
		{
			$fetchArgument === null ? $statement->setFetchMode($fetchStyle) : $statement->setFetchMode($fetchStyle, $fetchArgument);
		}

		$statement->execute($this->_params);

		if ($one)
		{
			$result = $statement->fetch();

			$result === false and $result = null;
		}
		else
		{
			$result = $fetchArgument === null ? $statement->fetchAll($fetchStyle) : $statement->fetchAll($fetchStyle, $fetchArgument);
		}

		$statement->closeCursor();

		return $result;
	}

	public function execute(): int
	{
		$connector = F::$mysql->getConnector($this->_connector);

		$pdo = $connector->pdo;

		$statement = $pdo->prepare($this->_sql);

		$ok = $statement->execute($this->_params);

		F::assert($ok, "Ошибка при выполнении запроса $this->_sql");

		$rows = $statement->rowCount();

		return $rows;
	}

	protected function _checkParams()
	{
		foreach ($this->_params as $key => $param)
		{
			if (! str_contains($this->_sql, $key))
			{
				unset($this->_params[$key]);
			}
		}
	}

}
