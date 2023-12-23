<?php
namespace myframework;

/*
 * Публичные фетчеры сводятся к этим приватным.
 */
trait CModelPrivateFetchers
{
	private function _fetchOneIntoModel(): ?CModel
	{
		$query = $this->_createQuery();

		$result = $query->fetchOneIntoModel($this);

		return $result;
	}

	private function _fetchAllClass(string $className = null): array
	{
		$className or $className = get_class($this);

		$query = $this->_createQuery();

		$result = $query->fetchAllClass($className);

		return $result;
	}

	private function _fetchAllArrayPlain(): array
	{
		$query = $this->_createQuery();

		$result = $query->fetchAllArrayPlain();

		return $result;
	}

	private function _createQuery(): CMysqlQuery
	{
		$this->_modelData->_sql = $this->_collectQuery();

		$query = new CMysqlQuery($this->_modelData->_sql);

		$query->params($this->_modelData->_params);

		return $query;
	}

}
