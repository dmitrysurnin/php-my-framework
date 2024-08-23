<?php
namespace www;
/** @var $this AuthController */
/** @var $errors array */
?>

<div class="login">

	<h2>Авторизация</h2>

	<form method="post" id="form-recaptcha">

		<div>
			<label>Почта</label>
			<div>
				<input type="text" name="email" class="<?= isset($errors['email']) ? 'error' : '' ?>" maxlength="<?= Auth::MAX_EMAIL_LENGTH ?>" placeholder="ваша электронная почта" value="<?= $_POST['email'] ?? $this->session->user_email ?>"/>
				<span class="error"><?= $errors['email'] ?? '' ?></span>
			</div>
		</div>

		<div>
			<label>Пароль</label>
			<div>
				<input type="password" name="password" class="<?= isset($errors['password']) ? 'error' : '' ?>" maxlength="<?= Auth::MAX_PASSWORD_LENGTH ?>" placeholder="ваш пароль" value="<?= $_POST['password'] ?? '' ?>"/>
				<span class="error"><?= $errors['password'] ?? '' ?></span>
			</div>
		</div>

		<?php if (CaptchaAction::getCount('login') >= Auth::CAPTCHA_LOGIN_COUNT) : ?>
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
				<button type="submit" class="default send">Войти</button>
				<div class="help-links">
					<a href="/auth/restore" class="reg">забыли пароль?</a>
				</div>
			</div>
		</div>

		<div class="bottom-link">
			<p>
				<a href="/auth/register">Зарегистрируйтесь</a>, если у вас нет аккаунта.
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

	$('input[name="email"]').val() || $('input[name="email"]').val('admin@mysite.ru');
	$('input[name="password"]').val() || $('input[name="password"]').val('qwerty');

})();
</script>
