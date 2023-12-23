<?php
namespace admin;
/** @var $this ArticlesController */
/** @var $data Article[] */
?>

<table class="table table-main table-bordered table-condensed">
	<thead>
		<tr>
			<th style="width: 100px;">
				<?= $this->htmlSortedHeader('Id', 't.id') ?>
			</th>
			<th style="width: 100px;">
				<?= $this->htmlSortedHeader('User Id', 't.user_id') ?>
			</th>
			<th>
				<?= $this->htmlSortedHeader('Титл', 't.title') ?>
			</th>
			<th>
				<?= $this->htmlSortedHeader('Создано', 't.created') ?>
			</th>
			<th>
				<?= $this->htmlSortedHeader('Изменено', 't.updated') ?>
			</th>
			<th></th>
		</tr>
		<?php if ($this->showFilters) : ?>
			<tr>
				<th>
					<?= $this->htmlFilterInput('t.id', ['placeholder' => '=']); ?>
				</th>
				<th>
					<?= $this->htmlFilterInput('t.user_id', ['placeholder' => '=']); ?>
				</th>
				<th>
					<?= $this->htmlFilterInput('t.title', ['placeholder' => '%%']); ?>
				</th>
				<th></th>
				<th></th>
				<th class="nowrap center middle" style="width: 1%;">
					<button class="btn btn-mini filter_clear">очистить</button>
				</th>
			</tr>
		<?php endif; ?>
	</thead>
	<tbody>
		<?php foreach ($data as $one) : ?>
			<tr>
				<td><?= $one->id ?></td>
				<td><?= $one->user_id ?></td>
				<td><?= $one->title ?></td>
				<td class="nowrap"><?= $one->created ?></td>
				<td class="nowrap"><?= $one->updated ?></td>
				<td class="center nowrap">
					<a class="fa fa-pencil" href="/articles/edit/<?= $one->id ?>" title="редактировать"></a>
					<a class="fa fa-trash-o" href="/articles/delete/<?= $one->id ?>" title="удалить"></a>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
