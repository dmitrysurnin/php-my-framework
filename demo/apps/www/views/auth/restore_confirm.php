<?php
namespace www;
/** @var $this AuthController */
/** @var $token Token */
/** @var $errors array */
?>

<div class="restore-complete">

	<h2>Восстановление пароля</h2>

	<form method="post" action="/auth/restoreconfirm/<?= $token->hash ?>">

		<input type="hidden" name="hash" value="<?= $token->hash ?>"/>

		<div>
			<label>Новый пароль</label>
			<div>
				<input type="password" name="password" class="<?= ! empty($errors['password']) ? 'error' : '' ?>" maxlength="<?= Auth::MAX_PASSWORD_LENGTH ?>" placeholder="пароль не менее 6 символов" autocomplete="off" value="<?= $_POST['password'] ?? '' ?>"/>
				<span class="error"><?= ! empty($errors['password']) ? $errors['password'] : '' ?></span>
			</div>
		</div>

		<div>
			<label>Подтвердите</label>
			<div>
				<input type="password" name="passwordRepeat" class="<?= ! empty($errors['passwordRepeat']) ? 'error' : '' ?>" maxlength="<?= Auth::MAX_PASSWORD_LENGTH ?>" placeholder="пароль ещё раз" autocomplete="off" value="<?= $_POST['passwordRepeat'] ?? '' ?>"/>
				<span class="error"><?= ! empty($errors['passwordRepeat']) ? $errors['passwordRepeat'] : '' ?></span>
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
(function () {

	$(document)
		.on('click', 'button.send', function(e) {
			$(this).addClass('loading');
		});

})();
</script>
