<?php
namespace myframework;

trait CControllerHtmlInputs
{
	protected array $_errors = [];

	/**
	 */
	public function htmlSelect(string $name, string $value, array $data, array $attributes = [], $hasEmptyOption = true): string
	{
		if (! empty($this->_errors[$name]))
		{
			if (isset($attributes['class']))
			{
				$attributes['class'] .= ' error';
			}
			else
			{
				$attributes['class'] = 'error';
			}
		}

		$html = '<select name="' . $name . '"';

		foreach ($attributes as $attribute => $val)
		{
			$html .= ' ' . $attribute . '="' . $val . '"';
		}

		$html .= '>';

		if ($hasEmptyOption)
		{
			$html .= '<option value=""></option>';
		}

		foreach ($data as $id => $item)
		{
			$html .= '<option value="' . $id . '"' . ($value === "$id" ? ' selected="selected"' : '') . '>' . ($item ?: '#' . $id) . '</option>';
		}

		$html .= '</select>';

		return $html;
	}

	/**
	 */
	public function htmlSelectNoEmpty(string $name, string $value, array $data, array $attributes = []): string
	{
		return $this->htmlSelect($name, $value, $data, $attributes, false);
	}

	/**
	 */
	public function htmlInput(string $name, ?string $value, array $attributes = []): string
	{
		$html = '<input name="' . $name . '" value="' . $value . '"';

		foreach ($attributes + ['type' => 'text', 'placeholder' => '', 'maxlength' => '255'] as $tag => $val)
		{
			$html .= ' ' . $tag . '="' . $val . '"';
		}

		return $html . '>';
	}

	/**
	 */
	public function htmlInputHidden(string $name, string $value, array $attributes = []): string
	{
		$html = $this->htmlInput($name, $value, [
				'type' => 'hidden',
			] + $attributes);

		return $html;
	}

	/**
	 */
	public function htmlCheckbox(string $name, string $value, array $attributes = []): string
	{
		$html = '<input name="' . $name . '" value="1"' . ($value ? ' checked="checked"' : '');

		foreach ($attributes + ['type' => 'checkbox'] as $tag => $val)
		{
			$html .= ' ' . $tag . '="' . $val . '"';
		}

		return $html . '>';
	}

	/**
	 */
	public function htmlDate(string $name, ?string $value, string $format = 'yyyy-mm-dd', array $attributes = []): string
	{
		$html = '<input name="' . $name . '" value="' . $value . '"';

		isset($attributes['style']) or $attributes['style'] = '';

		$attributes['style'] .= 'width: 85px;';

		isset($attributes['class']) or $attributes['class'] = '';

		$attributes['class'] = ltrim($attributes['class'] . ' bootstrap-datepicker');

		$attributes['data-format'] = $format;

		$attributes['autocomplete'] = 'off';

		foreach ($attributes + ['type' => 'text', 'placeholder' => '', 'maxlength' => '10'] as $tag => $val)
		{
			$html .= ' ' . $tag . '="' . $val . '"';
		}

		return $html . '>';
	}

	/**
	 */
	public function htmlTextarea(string $name, ?string $value, array $attributes = []): string
	{
		$html = '<textarea name="' . $name . '"';

		foreach ($attributes + ['placeholder' => ''] as $tag => $val)
		{
			$html .= ' ' . $tag . '="' . $val . '"';
		}

		return $html . '>' . $value . '</textarea>';
	}

	/**
	 */
	public function htmlFileUpload(string $name, ?string $value, array $attributes = []): string
	{
		$text = $attributes['placeholder'] ?? 'Выбрать';

		unset($attributes['placeholder']);

		$attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' btn file-upload' : 'btn file-upload';

		$attributes += ['href' => ''];

		$html = '<a';

		foreach ($attributes as $tag => $val)
		{
			$html .= ' ' . $tag . '="' . $val . '"';
		}

		$html .= '>' . $text . '</a>';

		$html .= '<input type="file" value=""/>';

		$html .= '<input type="hidden" name="' . $name . '" value="' . $value . '"/>';

		$html .= '<div class="file-upload-progress progress"><div class="bar"></div></div>';

		$html .= '';

		return $html;
	}

}
