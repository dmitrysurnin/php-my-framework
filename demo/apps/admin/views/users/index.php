<?php
namespace admin;
/** @var $this UsersController */
/** @var $data User[] */
?>

<p>
	Список пользователей (таблица <b>phpapp.users</b>)
</p>

<?php
$this->renderTable('_inner_table', [
	'data' => $data,
]);
?>

<?php if (! $this->request->isAjax) : ?>

<script type="text/javascript">
(function(p) {

})(window.p);
</script>

<?php endif;
