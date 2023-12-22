<?php
namespace www;
/** @var $this Mailer */
/** @var $link string */
/** @var $name string */
/** @var $email string */
/** @var $password string */
?>

<p style="padding: 20px 24px 0;"><b>Здравствуйте, <?= $name ?>!</b></p>

<p style="padding: 0 24px;">Вы зарегистрировались на сайте ....</p>

<p style="padding: 0 24px;">Для завершения регистрации вам необходимо подтвердить адрес электронной почты:</p>

<p style="padding: 5px 54px 20px;"><a href="<?= $link ?>" target="_blank" style="display: inline-block; padding: 10px 20px; border-radius: 7px; background-color: #d8f8ff; color: #0273ed; text-decoration: none;">подтвердить адрес электронной почты</a></p>

<p style="padding: 0 24px;">Ваша почта для входа: <b><a style="color: black;"><?= $email ?></a></b></p>

<p style="padding: 0 24px;">С уважением, <a href="https://mysite.ru/" target="_blank">...</a></p>

<p style="padding: 0 24px; font-size: 12px">Если ваш адрес был указан ошибочно, не предпринимайте никаких действий. Он будет автоматически удален из нашей базы.</p>
