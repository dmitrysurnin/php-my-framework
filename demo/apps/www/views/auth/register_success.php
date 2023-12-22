<?php
namespace www;
/** @var $this AuthController */
/** @var $email string */
?>

<div class="register_success">
  На адрес <b><?= $email ?></b> выслано письмо для подтверждения регистрации.
</div>

<div>
	<br>
	<hr>
	<br>
	Содержимое письма:
	<?php echo $GLOBALS['email_content_out']; ?>
</div>
