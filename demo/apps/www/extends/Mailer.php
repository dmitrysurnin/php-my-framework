<?php
namespace www;

/*
 * Здесь пример для smtp.bz, но можно использовать и любой другой smtp-сервис.
 * https://smtp.bz/panel/
 */
class Mailer extends \myframework\CMailer
{
	use \myframework\CRender;

	public function sendRegister(string $name, string $email, string $token): void
	{
		$mail = $this->_getPhpMailObject();

		$mail->addAddress($email); /// Add a recipient

		$mail->Subject = 'Регистрация';

		$mail->Body = $this->renderPage('mail_template', 'mail/register', [
			'title' => 'Регистрация',
			'link' => 'http://localhost:8500/' . '/token/confirm/' . $token,
			'name' => $name,
			'email' => $email,
		], true);

		$GLOBALS['email_content_out'] = $mail->Body; return; /// todo: убрать

		$mail->send();
	}

	public function sendRestore(string $email, string $token): void
	{
		$mail = $this->_getPhpMailObject();

		$mail->addAddress($email); /// Add a recipient

		$mail->Subject = 'Восстановление пароля';

		$mail->Body = $this->renderPage('mail_template', 'mail/restore', [
			'title' => 'Восстановление пароля',
			'link' => 'http://localhost:8500/' . '/token/confirm/' . $token,
			'email' => $email,
		], true);

		$GLOBALS['email_content_out'] = $mail->Body; return; /// todo: убрать

		$mail->send();
	}

	private function _getPhpMailObject(): \PHPMailer\PHPMailer\PHPMailer
	{
		$mail = new \PHPMailer\PHPMailer\PHPMailer(true);

		$mail->SMTPDebug = 0; /// 0 = off, 1 = basic, 2 = advanced verbose debug output

		$mail->CharSet = 'utf-8';

		$mail->isSMTP(); /// Set mailer to use SMTP

		$mail->Host = 'connect.smtp.bz'; /// Specify main SMTP server

		$mail->SMTPAuth = true; /// Enable SMTP authentication

		$mail->Username = 'noreply@mysite.ru'; /// SMTP username

		$mail->Password = 'mysmtppassword'; /// SMTP password

		$mail->SMTPSecure = 'tls'; /// Enable TLS encryption, 'ssl' also accepted

		$mail->Port = 587; /// TCP port to connect to

		$mail->setFrom('noreply@mysite.ru', 'MySite'); /// Set sender of the mail

		$mail->isHTML(true); /// Set email format to HTML

		return $mail;
	}

}
