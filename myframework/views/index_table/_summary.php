<?php
/** @var $this \myframework\CAdminController */
?>

<div class="summary">

	<span class="ajax-loading-spinner">
		<i class="fa fa-spinner fa-spin"></i>
	</span>

	<button class="btn btn-mini clear_filters">
		сбросить фильтры
	</button>

	показывать <input type="text" class="items_per_page" style=" width: 42px; padding: 0 6px; text-align: right;" value="<?= $this->_itemsPerPage ?>"/>

	<button class="btn btn-mini refresh_table">
		обновить таблицу
	</button>

	<?= ($this->showRealRowsCount ? 'всего ' : 'в таблице около ') . $this->totalRows ?> элементов

</div>
