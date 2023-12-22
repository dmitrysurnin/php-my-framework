<?php
namespace admin;
/** @var $this UsersController */
/** @var $data User[] */

//var_dump(debug_backtrace());
//debug_print_backtrace();
//exit;
?>

<table class="table table-main table-bordered table-condensed">
	<thead>
		<tr>
			<th style="width: 100px;">
				<?= $this->htmlSortedHeader('Id', 't.id') ?>
			</th>
			<th>
				<?= $this->htmlSortedHeader('Имя', 't.name') ?>
			</th>
			<th>
				<?= $this->htmlSortedHeader('Email', 't.email') ?>
			</th>
			<th>
				<?= $this->htmlSortedHeader('Админ', 't.is_admin') ?>
			</th>
			<th>
				<?= $this->htmlSortedHeader('Подтв.', 't.email_confirmed') ?>
			</th>
			<th>
				<?= $this->htmlSortedHeader('Зарегистрирован', 't.created') ?>
			</th>
			<th>
				<?= $this->htmlSortedHeader('Статей', 'count_articles') ?>
			</th>
			<th></th>
		</tr>
		<?php if ($this->showFilters) : ?>
			<tr>
				<th>
					<?= $this->htmlFilterInput('t.id', ['placeholder' => '=']); ?>
				</th>
				<th>
					<?= $this->htmlFilterInput('t.name', ['placeholder' => '%%']); ?>
				</th>
				<th>
					<?= $this->htmlFilterInput('t.email', ['placeholder' => '%%']); ?>
				</th>
				<th>
					<?= $this->htmlFilterSelect('t.is_admin', [ 0 => 'нет', 1 => 'да' ], [
						'style' => 'width: 60px;',
					]); ?>
				</th>
				<th>
					<?= $this->htmlFilterSelect('t.email_confirmed', [ 0 => 'нет', 1 => 'да' ], [
						'style' => 'width: 60px;',
					]); ?>
				</th>
				<th>
					<?= $this->htmlFilterDate('created_from', 'yyyy-mm-dd', ['placeholder' => 'с']); ?>
					<?= $this->htmlFilterDate('created_to', 'yyyy-mm-dd', ['placeholder' => 'по']); ?>
				</th>
				<th>
					<?= $this->htmlFilterInput('count_articles', ['placeholder' => '=', 'style' => 'width:100px;']); ?>
				</th>
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
				<td><?= $one->name ?></td>
				<td><?= $one->email ?></td>
				<td><?= $one->is_admin ? 'да' : '' ?></td>
				<td><?= $one->email_confirmed ? 'да' : '' ?></td>
				<td class="nowrap"><?= $one->created ?></td>
				<td>
					<?= $one->count_articles ?>
					<?php if ($one->count_articles) : ?>
						<a href="/articles?filter[t.user_id]=<?= $one->id ?>">посмотреть</a>
					<?php endif; ?>
				</td>
				<td class="center nowrap">
					<a class="fa fa-pencil" href="/users/edit/<?= $one->id ?>" title="редактировать"></a>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
