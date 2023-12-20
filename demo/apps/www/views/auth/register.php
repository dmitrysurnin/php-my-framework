<?php
namespace www;
/** @var $this AuthController */
/** @var $errors array */
?>

<div class="login">

	<h2>Регистрация</h2>

	<form method="post">

		<div>
			<label>Имя</label>
			<div>
				<input type="text" name="name" class="<?= ! empty($errors['name']) ? 'error' : '' ?>" maxlength="<?= Auth::MAX_NAME_LENGTH ?>" placeholder="ваше имя" value="<?= $_POST['name'] ?? $this->session->user_name ?>"/>
				<span class="error"><?= ! empty($errors['name']) ? $errors['name'] : '' ?></span>
			</div>
		</div>

		<div>
			<label>Почта</label>
			<div>
				<input type="text" name="email" class="<?= ! empty($errors['email']) ? 'error' : '' ?>" maxlength="<?= Auth::MAX_EMAIL_LENGTH ?>" placeholder="ваша электронная почта" value="<?= $_POST['email'] ?? $this->session->user_email ?>"/>
				<span class="error"><?= ! empty($errors['email']) ? $errors['email'] : '' ?></span>
			</div>
		</div>

		<div>
			<label>Пароль</label>
			<div>
				<input type="password" name="password" class="<?= ! empty($errors['password']) ? 'error' : '' ?>" maxlength="<?= Auth::MAX_PASSWORD_LENGTH ?>" placeholder="пароль не менее <?= Auth::MIN_PASSWORD_LENGTH ?> символов" value="<?= $_POST['password'] ?? '' ?>"/>
				<span class="error"><?= ! empty($errors['password']) ? $errors['password'] : '' ?></span>
			</div>
		</div>

		<?php if (CaptchaAction::getCount('register') >= Auth::CAPTCHA_REGISTER_COUNT) : ?>
			<div>
				<label class="empty"></label>
				<div class="recaptcha-cont">
					<div class="g-recaptcha" data-sitekey="6LdSqTApAAAAAElhnJ9kIFiXDpdtj7sjMX5eXukJ"></div>
					<span class="error"><?= ! empty($errors['captcha']) ? $errors['captcha'] : '' ?></span>
					<script src="https://www.google.com/recaptcha/api.js?hl=ru" async defer></script>
				</div>
			</div>
		<?php endif ?>

		<div>
			<label></label>
			<div>
				<button type="submit" class="default send">Зарегистрироваться</button>
			</div>
		</div>

		<div class="bottom-link">
			<p>
				Если вы уже зарегистрированы, <a href="/auth">войдите</a>.
			</p>
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
