<?php
namespace myframework;

/**
 */
class CMysqlInstance extends CSingleton
{
	protected array $_config = [];

	/** @var CMysqlConnector[] */
	protected array $_connectors = [];

	protected int $_savepoint = 0;

	protected CMysqlTransaction $_transaction;

	public function getConnector(string $name): CMysqlConnector
	{
		if (! isset($this->_connectors[$name]))
		{
			if (! $this->_config)
			{
				$this->_config = require_once APP . '/config/mysql.php';
			}

			F::assert(isset($this->_config[$name]), "Не указано $name в конфиге mysql.");

			$this->_connectors[$name] = new CMysqlConnector($this->_config[$name]);
		}

		return $this->_connectors[$name];
	}

	public function query($sql)
	{
		return new CMysqlQuery($sql);
	}

	public function beginTransaction(): CMysqlTransaction
	{
		if ($this->_savepoint ++) /// вложенная транзакция
		{
			$query = new CMysqlQuery('SAVEPOINT sp_' . ($this->_savepoint - 1));

			$query->execute();
		}
		else
		{
			$query = new CMysqlQuery('START TRANSACTION');

			$query->execute();

			$this->_transaction = new CMysqlTransaction();
		}

		return $this->_transaction;
	}

	public function inTransaction(): bool
	{
		return $this->_savepoint > 0;
	}

	public function commitTransaction(): void
	{
		F::assert($this->_savepoint > 0);

		if (-- $this->_savepoint) /// вложенная транзакция
		{
			$query = new CMysqlQuery('RELEASE SAVEPOINT sp_' . $this->_savepoint);

			$query->execute();
		}
		else
		{
			$query = new CMysqlQuery('COMMIT');

			$query->execute();
		}
	}

	public function rollbackTransaction(): void
	{
		F::assert($this->_savepoint > 0);

		if (-- $this->_savepoint) /// вложенная транзакция
		{
			$query = new CMysqlQuery('ROLLBACK TO SAVEPOINT sp_' . $this->_savepoint);

			$query->execute();
		}
		else
		{
			$query = new CMysqlQuery('ROLLBACK');

			$query->execute();
		}
	}

	public function rollbackAllTransactions(): void
	{
		if ($this->_savepoint > 0)
		{
			while (-- $this->_savepoint) /// вложенная транзакция
			{
				$query = new CMysqlQuery('ROLLBACK TO SAVEPOINT sp_' . $this->_savepoint);

				$query->execute();
			}

			$query = new CMysqlQuery('ROLLBACK');

			$query->execute();
		}
	}

	public function lastInsertId($connector = 'master'): string
	{
		$connector = F::$mysql->getConnector($connector);

		return $connector->pdo->lastInsertId();
	}

}
