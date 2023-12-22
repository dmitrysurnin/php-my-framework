<?php
namespace www;
/** @var $this AuthController */
/** @var $email string */
?>

<div class="register_success">
  На адрес <b><?= $email ?></b> выслано письмо с инструкцией по восстановлению пароля.
</div>

<div>
	<br>
	<hr>
	<br>
	Содержимое письма:
	<?php echo $GLOBALS['email_content_out']; ?>
</div>
