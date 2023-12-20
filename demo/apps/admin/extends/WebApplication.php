<?php
namespace admin;

class WebApplication extends \myframework\CWebApplication
{
	public function __construct(array $config)
	{
		parent::__construct($config);

		$this->session = HttpSession::inst(); /// создать экземпляр класса, но не стартуем его пока
	}

	public function handleException(\Throwable $exception)
	{
		restore_error_handler(); /// disable error capturing to avoid recursive errors

		restore_exception_handler(); /// disable error capturing to avoid recursive errors

		ErrorHandler::inst()->handleException($exception);
	}

	public function handleError($code, $message, $file, $line)
	{
		restore_error_handler(); /// disable error capturing to avoid recursive errors

		restore_exception_handler(); /// disable error capturing to avoid recursive errors

		ErrorHandler::inst()->handleError($code, $message, $file, $line);
	}

}
