<?php
namespace myframework;

trait CModelPrivateFetchers
{
	private function _fetchOneIntoModel(): ?CModel
	{
		$this->_modelData->_sql = $this->_collectQuery();

		$query = new CMysqlQuery($this->_modelData->_sql);

		$query->params($this->_modelData->_params);

		$result = $query->fetchOneIntoModel($this);

		return $result;
	}

	private function _fetchAllClass(string $className = null): array
	{
		$className or $className = get_class($this);

		$this->_modelData->_sql = $this->_collectQuery();

		$query = new CMysqlQuery($this->_modelData->_sql);

		$query->params($this->_modelData->_params);

		$result = $query->fetchAllClass($className);

		return $result;
	}

	private function _fetchAllArrayPlain(): array
	{
		$this->_modelData->_sql = $this->_collectQuery();

		$query = new CMysqlQuery($this->_modelData->_sql);

		$query->params($this->_modelData->_params);

		$result = $query->fetchAllArrayPlain();

		return $result;
	}

}
