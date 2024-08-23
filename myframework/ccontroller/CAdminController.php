<?php
namespace myframework;

class CAdminController extends CController
{
	public CModel $model;

	public bool $showFilters = true;

	public bool $showSummary = true;

	protected bool $_showPagination = true;

	public bool $showRealRowsCount = true;

	public int $timerStart;

	public array $filter = [];

	public int $page = 1;

	public int $totalRows;

	protected int $_itemsPerPage = 20;

	public int $pagesNum;

	protected string $_sort = 't.id';

	protected int $_order = -1; /// -1 = DESC, 1 = ASC

	protected array $_addToContent = [];

	protected array $_addToFilter = [];

	protected function _before()
	{
		parent::_before();

		$this->request->data('filter') and $this->filter += $this->request->data('filter');
	}

	public function actionIndex()
	{
		$data = $this->_actionIndexGetData();

		$this->_actionIndexRenderData($data);
	}

	protected function _actionIndexGetData(): array
	{
		$this->_processFiltersAndPagination();

		if ($this->showRealRowsCount)
		{
			$this->model->calcFoundRows();
		}

		$data = $this->model
			->order($this->_sort . ($this->_order >= 0 ? '' : ' DESC') . ($this->model->getOrder() ? ', ' . $this->model->getOrder() : ''))
			->findAll();

		if ($this->showRealRowsCount)
		{
			$q = F::$mysql->query('SELECT FOUND_ROWS() AS cnt');

			if ($connector = $this->model->getConnector())
			{
				$q->connector($connector);
			}

			$this->totalRows = $q->fetchOneArray()['cnt'];
		}
		else
		{
			$this->totalRows = $this->model->findApproximateCount();
		}

		$this->pagesNum = (int) ceil($this->totalRows / $this->_itemsPerPage);

		$this->pagesNum > 0 or $this->pagesNum = 1;

		return $data;
	}

	protected function _actionIndexRenderData(array $data)
	{
		$this->renderData('index', $this->_addToContent + [
			'data' => $data,
		]);
	}

	public function actionView($id)
	{
		$this->renderData('view', $this->_addToContent + [
			'model' => $this->model->findById($id),
		]);
	}

	public function actionEdit(int $id = 0)
	{
		$model = $this->model;

		if ($id)
		{
			$this->model->findById($id);
		}
		else
		{
			$this->model->setAttributesFromSelect();
		}

		$this->_errors = [];

		if ($post = $_POST)
		{
			$model->id = $id;

			$this->_trimValues($post);

			if (! ($this->_errors = $model->validate($post)))
			{
				$returnTo = $post['return_to'] ?? null;

				unset($post['id'], $post['return_to']);

				try
				{
					$model->save($post);
				}
				catch (\Exception $e)
				{
					$this->_errors['Ошибка'] = 'Не удалось выполнить запрос: ' . $e->getMessage();
				}

				if (! $this->_errors)
				{
					if (! $this->request->isAjax)
					{
						$this->redirect($returnTo);
					}
					else
					{
//						$model->reload(); /// обновить данные модели для отображения в случае ajax
					}
				}
			}
		}

		$model->setAttributes($post); /// если был POST и мы оказались тут, значит выше произошла ошибка (ошибка валидации либо mysql-error при сохранении). тогда установить значения POST в модель, чтобы во вьюшке они оказались в полях

		$this->head[] = $this->render(F_ROOT . '/views/edit/_edit_scripts', [], true);

		$this->renderData('edit', $this->_addToContent + [
			'id' => $id,
			'model' => $model,
			'errors' => $this->_errors,
		]);
	}

	protected function _trimValues(array &$post)
	{
		foreach ($post as &$value)
		{
			if (is_string($value))
			{
				$value = remove_invisible_symbols($value);
			}
		}
	}

	public function actionDelete($id)
	{
		$this->request->isAjax or $this->redirect('/' . $this->controller);

		$this->model->deleteById($id);

		$this->model->init();

		$this->actionIndex();
	}

	public function actionToggle($id)
	{
		$this->actionHide($id);
	}

	public function actionHide(int $id)
	{
		$this->request->isAjax or $this->redirect('/' . $this->controller);

		/** @var CModel $className */
		$className = get_class($this->model);

		$className::model()->saveAttributesById($id, [
			'hidden = 1 - hidden',
		]);

		$this->actionIndex();
	}

	protected function _processFiltersAndPagination()
	{
		if ($this->showFilters)
		{
			$this->_processFilters();
		}

		if ($this->filter)
		{
			$this->model->applyFilter($this->filter);
		}

		if ($this->_showPagination)
		{
			$this->page = (int) $this->request->data('page');

			$this->page >= 1 or $this->page = 1;

			$this->request->data('limit') and $this->_itemsPerPage = $this->request->data('limit');

			$this->_itemsPerPage > 0 or $this->_itemsPerPage = 20;

			$this->model->limit($this->_itemsPerPage);

			$this->model->offset(max(($this->page - 1) * $this->_itemsPerPage, 0));
		}
	}

	protected function _processFilters()
	{
		$this->filter += $this->_addToFilter;

		$this->request->data('sort') and $this->_sort = $this->request->data('sort');

		$this->request->data('order') and $this->_order = (int) $this->request->data('order');
	}

	public function renderTable($tableView, array $data = [])
	{
		$this->render(F_ROOT . '/views/index_table/_index_scripts');

		echo $pagination = $this->htmlPagination();

		if ($this->showSummary)
		{
			$this->render(F_ROOT . '/views/index_table/_summary');
		}

		$this->render($tableView, $data);

		echo $pagination;
	}

	public function htmlPagination(): string
	{
		if (! $this->_showPagination)
		{
			return '';
		}

		return $this->render(F_ROOT . '/views/index_table/_pagination', [], true);
	}

	public function htmlSortedHeader(string $text, string $name): string
	{
		return '<a class="sort" data-sort="' . $name . '" data-order="' . ($this->_sort == $name ? - $this->_order : $this->_order) . '" href="?page=' . $this->page . '&sort=' . $name . '&order=' . ($this->_sort == $name ? - $this->_order : $this->_order) . '">' . $text . ($this->_sort == $name ? '&nbsp;<i class="icon-arrow-' . ($this->_order == 1 ? 'down' : 'up') . '"></i>' : '') . '</a>';
	}

	public function htmlFilterInput(string $name, array $attributes = []): string
	{
		isset($this->filter[$name]) and $this->filter[$name] !== '' and $attributes['class'] = (isset($attributes['class']) ? $attributes['class'] . ' filtered' : 'filtered');

		$html = '<input name="filter[' . $name . ']"';

		foreach ($attributes + ['type' => 'text', 'placeholder' => ''] as $tag => $value)
		{
			$html .= ' ' . $tag . '="' . $value . '"';
		}

		$html .= ' value="' . (isset($this->filter[$name]) ? $this->filter[$name] : '') . '"';

		return $html . '>';
	}

	public function htmlFilterInputHidden(string $name, array $attributes = []): string
	{
		return $this->htmlFilterInput($name, [
			'type' => 'hidden',
		] + $attributes);
	}

	public function htmlFilterDate(string $name, string $format = 'yyyy-mm-dd', array $attributes = []): string
	{
		$html = '<input name="filter[' . $name . ']"';

		isset($attributes['style']) or $attributes['style'] = '';

		$attributes['style'] .= 'width: 85px;';

		isset($attributes['class']) or $attributes['class'] = '';

		$attributes['class'] = ltrim($attributes['class'] . ' bootstrap-datepicker');

		$attributes['data-format'] = $format;

		$attributes['autocomplete'] = 'off';

		foreach ($attributes + ['type' => 'text', 'placeholder' => ''] as $tag => $value)
		{
			$html .= ' ' . $tag . '="' . $value . '"';
		}

		$html .= ' value="' . (isset($this->filter[$name]) ? $this->filter[$name] : '') . '"';

		return $html . '>';
	}

	public function htmlFilterCheckbox(string $name, string $label): string
	{
		$html = '<label><input type="checkbox" name="filter[' . $name . ']"' . (isset($this->filter[$name]) ? ' checked' : '') . '>' . $label . '</label>';

		return $html;
	}

	public function htmlFilterSelect(string $name, array $data, array $attributes = []): string
	{
		isset($this->filter[$name]) and $this->filter[$name] !== '' and $attributes['class'] = (isset($attributes['class']) ? $attributes['class'] . ' filtered' : 'filtered');

		$html = '<select name="filter[' . $name . ']"';

		foreach ($attributes as $attribute => $value)
		{
			$html .= ' ' . $attribute . '="' . $value . '"';
		}

		$html .= '>';

		if (! isset($data['']))
		{
			$html .= '<option value="" class="gray">(' . count($data) . ')</option>';
		}

		foreach ($data as $id => $value)
		{
			$html .= '<option value="' . $id . '"' . (isset($this->filter[$name]) && (string) $this->filter[$name] === "$id" ? ' selected="selected"' : '');

			if (is_array($value))
			{
				$optionAttributes = $value;

				$value = array_shift($optionAttributes);

				foreach ($optionAttributes as $attributeName => $attributeValue)
				{
					$html .= ' ' . $attributeName . '="' . $attributeValue . '"';
				}
			}

			$html .= '>' . ($value !== '' ? $value : '#' . $id) . '</option>';
		}

		return $html . '</select>';
	}

	/**
	 */
	public function htmlErrors(): string
	{
		$html = '';

		if ($this->_errors)
		{
			$html .= '<div class="alert alert-error">';

			$html .= '<a class="close" data-dismiss="alert">×</a>';

			foreach ($this->_errors as $key => $message)
			{
				$html .= $key . ': ' . htmlspecialchars($message) . '<br>';
			}

			$html .= '</div>';
		}

		return $html;
	}

}
