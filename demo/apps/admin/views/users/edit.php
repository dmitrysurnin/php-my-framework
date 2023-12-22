<?php
namespace admin;
/** @var $this UsersController */
/** @var $id int */
/** @var $model User */
/** @var $errors array */

$returnTo = $_POST['return_to'] ?? ($_SERVER['HTTP_REFERER'] ?? $this->request->host);
?>

<p>
	<?= $id ? 'Редактировать' : 'Добавить нового' ?> пользователя
</p>

<form method="post">

	<?php if ($errors): ?>
		<div class="alert alert-error">
			<?php foreach ($errors as $error) : ?>
				<p><?= $error ?></p>
			<?php endforeach; ?>
		</div>
	<?php endif ?>

	<?= $this->htmlInputHidden('id', $id); ?>

	<?= $this->htmlInputHidden('return_to', $returnTo); ?>

	<div>
		<label>Имя</label>
		<div>
			<?= $this->htmlInput('name', $model->name, [
				'placeholder' => 'Имя',
				'maxlength' => 100,
				'class' => ! empty($errors['name']) ? 'error' : '',
			]); ?>
			<p class="error"><?= ! empty($errors['name']) ? $errors['name'] : '' ?></p>
		</div>
	</div>

	<div>
		<label>Email</label>
		<div>
			<?= $this->htmlInput('email', isset($_POST['email']) ? $_POST['email'] : ($model ? $model->email : ''), [
				'placeholder' => 'Email',
				'maxlength' => 100,
				'class' => ! empty($errors['email']) ? 'error' : '',
			]); ?>
			<p class="error"><?= ! empty($errors['email']) ? $errors['email'] : '' ?></p>
		</div>
	</div>

	<div>
		<label>Админ</label>
		<div>
			<?php echo $this->htmlSelect('is_admin', $model->is_admin, [ 0 => 'нет', 1 => 'да' ], [
				'class' => ! empty($errors['is_admin']) ? 'error' : '',
			]); ?>
			<p class="error"><?= ! empty($errors['is_admin']) ? $errors['is_admin'] : '' ?></p>
		</div>
	</div>

	<div>
		<div>
			<button type="submit" class="btn btn-primary">Сохранить</button>
		</div>
	</div>

</form>

<?php if (! $this->request->isAjax) : ?>

<script type="text/javascript">
(function(p) {

})(window.p);
</script>

<?php endif;
