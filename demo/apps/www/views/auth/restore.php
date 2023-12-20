<?php
namespace www;
/** @var $this AuthController */
/** @var $errors array */
?>

<div class="login">

	<h2>Восстановление пароля</h2>

	<form method="post">

		<div>
			<label>Почта</label>
			<div>
				<input type="text" name="email" class="<?= ! empty($errors['email']) ? 'error' : '' ?>" maxlength="<?= Auth::MAX_EMAIL_LENGTH ?>" placeholder="ваша электронная почта" value="<?= $_POST['email'] ?? $this->session->user_email ?>"/>
				<span class="error"><?= ! empty($errors['email']) ? $errors['email'] : '' ?></span>
			</div>
		</div>

		<div>
			<label class="empty"></label>
			<div class="recaptcha-cont">
				<div class="g-recaptcha" data-sitekey="6LdSqTApAAAAAElhnJ9kIFiXDpdtj7sjMX5eXukJ"></div>
				<span class="error"><?= ! empty($errors['captcha']) ? $errors['captcha'] : '' ?></span>
				<script src="https://www.google.com/recaptcha/api.js?hl=ru" async defer></script>
			</div>
		</div>

		<div>
			<label></label>
			<div>
				<button type="submit" class="default send">Отправить</button>
			</div>
		</div>

	</form>

</div>

<script type="text/javascript">
(function() {

	$(document)
		.on('click', 'button.send', function(e) {
			$(this).addClass('loading');
		});

})();
</script>
