<?php
namespace myframework;

class CCaptcha extends CSingleton
{
	/*
	 * В качестве капчи используется гугловая рекапча
	 * Здесь нужно не забыть сменить ключи на свои собственные (то есть зарегаться в рекапче и запросить ключ)
	 * Приватный ключ здесь, публичные во вьюшках.
	 * https://www.google.com/recaptcha/
	 */
	public static function checkCaptcha($captcha): bool
	{
		$params = [
			'secret' => '6LdSqTApAAAAAL7dIk0h7y0eLNEOOWHf_ITpv3Lw', /// todo: нужно изменить ключи на свои!
			'response' => $captcha,
		];

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');

		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($ch, CURLOPT_TIMEOUT, 5);

		$result = curl_exec($ch);

		curl_close($ch);

		$data = json_decode($result, true);

		return $data ? $data['success'] : false;
	}

}
