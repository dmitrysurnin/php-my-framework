<?php
/** @var $this \myframework\CAdminController */

list($page, $sort, $order, $pagesNum, $totalRows) = [ $this->page, $this->_sort, $this->_order, $this->pagesNum, $this->totalRows ];

$page >= 1 or $page = 1;

$page <= $pagesNum or $page = $pagesNum;
?>

<div class="pagination">
  <ul>
    <?php
		$borders = 3;

		$inner = 8;

		$begin = 1;

		$end = $pagesNum;

		$params = '&sort=' . $sort . '&order=' . $order . '';

		$html = '<li class="prev' . ($page == 1 ? ' disabled' : '') . '"><a data-page="' . ($page - 1) . '" href="?page=' . ($page - 1) . $params . '">«</a></li>';

		if ($page >= $borders + $inner + 3)
		{
			for ($i = 1; $i <= $borders; $i ++)
			{
				$html .= '<li><a data-page="' . $i . '" href="?page=' . $i . $params . '">' . $i . '</a></li>';
			}

			$html .= '<li class="disabled"><a>...</a></li>';

			$begin = $page - $inner;
		}

		if ($page <= $pagesNum - $inner - $borders - 2)
		{
			$end = $page + $inner;
		}

		for ($i = $begin; $i <= $end; $i ++)
		{
			$html .= '<li' . ($i == $page ? ' class="disabled"' : '') . '><a data-page="' . $i . '" href="?page=' . $i . $params . '">' . $i . '</a></li>';
		}

		if ($page <= $pagesNum - $borders - $inner - 2)
		{
			$html .= '<li class="disabled"><a>...</a></li>';

			if ($this->showRealRowsCount)
			{
				for ($i = $pagesNum - $borders + 1; $i <= $pagesNum; $i ++)
				{
					$html .= '<li><a data-page="' . $i . '" href="?page=' . $i . $params . '">' . $i . '</a></li>';
				}
			}
		}

		$html .= '<li class="prev' . ($page >= $pagesNum ? ' disabled' : '') . '"><a data-page="' . ($page + 1) . '" href="?page=' . ($page + 1) . $params . '">»</a></li>';

		echo $html;
		?>
  </ul>
</div>
