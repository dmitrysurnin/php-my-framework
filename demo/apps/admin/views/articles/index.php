<?php
namespace admin;
/** @var $this ArticlesController */
/** @var $data Article[] */
?>

<p>
	Список статей (таблица <b>phpapp.articles</b>)
</p>

<?php
$this->renderTable('_inner_table', [
	'data' => $data,
]);
?>

<div>
	<a class="btn" href="/articles/edit">добавить статью</a>
</div>

<?php if (! $this->request->isAjax) : ?>

<script type="text/javascript">
(function(p) {

})(window.p);
</script>

<?php endif;
