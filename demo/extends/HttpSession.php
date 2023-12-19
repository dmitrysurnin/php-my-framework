<?php
namespace super;

/**
 * Класс, к которому обращаться, чтобы прочитать и записать что-то в сессию, чтобы не использовать конструкции типа $abc = $_SESSION['abc'],
 * а использовать более красивое $abc = $this->session->abc.
 */
class HttpSession extends \myframework\CHttpSession
{
	public function start()
	{
///		ini_set('session.gc_probability', 1); /// необязательно, т.к. это default value

///		ini_set('session.gc_divisor', 100); /// необязательно, т.к. это default value

		ini_set('session.cookie_httponly', 1); /// если 1, то кука сессии будет недоступна из javascript (default is 0) /// https://stackoverflow.com/questions/8419332/proper-session-hijacking-prevention-in-php

//		ini_set('session.use_only_cookies', 1); /// Session ID cannot be passed through URLs /// необязательно, т.к. это default value

		ini_set('session.cookie_secure', 1); /// посылаьть куку только по https (default is 0) /// Allow access to the session ID cookie only when the protocol is HTTPS. If a website is only accessible via HTTPS, it should enable this setting.

		session_name('SID'); /// имя куки, к которой привязана сессия (default is PHPSESSID)

		session_set_cookie_params(315360000, '/', F::$app->request->domain ? '.' . F::$app->request->domain: null); /// установка куки сессии на 315360000 секунд = 10 лет
		/// однако хром не позволяет установить любую куку более чем на 400 дней: https://stackoverflow.com/a/73557227/1378713

		$sessionHandler = new HttpSessionHandler(); /// http://php.net/manual/ru/function.session-set-save-handler.php

		session_set_save_handler($sessionHandler);

		session_start();

		$this->sid = session_id(); /// значение куки SID

		/// sid это устройство (браузер). то есть это кука, которая хранится в браузере всегда и не меняется никогда, таким образом привязана к конкретному устройству (браузеру)
		/// если юзер вдруг почистил все куки в своём браузере, то считаем, что это теперь типа новое устройство, а старое устройство он выбросил (sid будет новое)
		/// эта кука устанавливается при посещении любой страницы и далее никогда не меняется (до очистки кук юзером)
		$_COOKIE['SID'] = $this->sid; /// если не записать в массив $_COOKIE, то оно там окажется только по завершении скрипта (т.к. было создано чуть выше), а надо иметь уже сейчас
	}

}
