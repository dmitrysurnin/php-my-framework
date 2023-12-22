<?php
namespace admin;
/** @var $this ArticlesController */
/** @var $id int */
/** @var $model Article */
/** @var $errors array */

$returnTo = $_POST['return_to'] ?? ($_SERVER['HTTP_REFERER'] ?? $this->request->host);
?>

<p>
	<?= $id ? 'Редактировать' : 'Добавить новую' ?> статью
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
		<label>Титл</label>
		<div>
			<?= $this->htmlInput('title', $model->title, [
				'placeholder' => 'Титл',
				'class' => ! empty($errors['title']) ? 'error' : '',
			]); ?>
			<p class="error"><?= ! empty($errors['title']) ? $errors['title'] : '' ?></p>
		</div>
	</div>

	<div>
		<label>Контент</label>
		<div>
			<?= $this->htmlTextarea('content', $model->content, [
				'placeholder' => 'Контент',
				'class' => ! empty($errors['content']) ? 'error' : '',
				'style' => 'width:400px; height:100px;',
			]); ?>
			<p class="error"><?= ! empty($errors['content']) ? $errors['content'] : '' ?></p>
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
