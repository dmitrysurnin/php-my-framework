<?php
namespace super;

/*
 * Кастомный сохранятель данных сессий, чтобы писать данные сессий в базу данных, чтобы они не стирались при переезде на другой сервер например.
 * PHP записывает сессию всякий раз при завершении работы php-скрипта.
 */
class HttpSessionHandler extends \myframework\CHttpSessionHandler
{
	public function read($id): string
	{
		$session = Session::loadBySid($id);

		if ($session)
		{
			$GLOBALS['session_id'] = $session->id;

			return $GLOBALS['session_data'] = (string) $session->data;
		}

		$GLOBALS['session_id'] = Session::createEmptySid($id); /// сразу создать, чтобы ниже делать не insert on duplicate key update, а простой update

		return $GLOBALS['session_data'] = '';
	}

	public function write($id, $data): bool
	{
		if ($GLOBALS['session_data'] != $data)
		{
			return Session::updateDataBySid($id, $data);
		}

		return true;
	}

}
