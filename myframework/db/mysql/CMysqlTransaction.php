<?php
namespace myframework;

class CMysqlTransaction
{
	/** @var bool */
	protected $_active;

	public function __construct()
	{
		$this->_active = true;
	}

	public function commit()
	{
		F::$mysql->commitTransaction();
	}

	public function rollback()
	{
		F::$mysql->rollbackTransaction();
	}

}
