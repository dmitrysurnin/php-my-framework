<?php
namespace myframework;

class CAssertException extends \Exception
{
	public function getPlace()
	{
		$traces = $this->getTrace();

		return [ $traces[0]['file'], $traces[0]['line'], $this->getMessage() ];
	}

}
