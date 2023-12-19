<?php
namespace myframework;

class CMailer extends CSingleton
{
	public function send($to, $subject, $body, $params = null)
	{
		$headers =
			"MIME-Version: 1.0\n" .
			"Content-type: text/html; charset=utf-8";

		$body = $this->wrap($body);

		return mail($to, $subject, $body, $headers, $params);
	}

	/**
	 * Строки не должны быть длиной свыше 998 символов (1000 с CRLF), иначе вставятся автоматические переносы строк, хэш контента изменится, и dkim письма станет неверным (будет не: dkim=pass, а: dkim=neutral, body hash did not verify)
	 * https://tools.ietf.org/html/rfc2822#section-2.1.1
	 * @param $body
	 * @param int $length
	 * @return string
	 */
	protected function wrap($body, $length = 998)
	{
		return wordwrap($body, $length);
	}

}
