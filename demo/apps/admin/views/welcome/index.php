<?php
namespace admin;
/** @var $this WelcomeController */
?>

<p>
	IP сервера: <?= $_SERVER['SERVER_ADDR'] ?>
</p>
<p>
	Ваш IP: <?= $_SERVER['REMOTE_ADDR']; ?>
</p>
<p>
	<?php $free = disk_free_space('/'); $total = disk_total_space('/') ?>
	На диске свободно <?= round($free / 1073741824, 1) ?> GB из <?= round($total / 1073741824, 1) ?> GB (<?= round($free * 100 / $total, 1) ?> %)
</p>

