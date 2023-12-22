<?php
namespace www;

class ErrorHandler extends \super\ErrorHandler
{
	public HttpSession $session;

	protected function __construct()
	{
		$this->session = HttpSession::inst();
	}

}
