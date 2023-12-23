<?php
namespace myframework;

/*
 * Тут валидация модели перед созданием/изменением.
 */
trait CModelValidate
{
	public function validate(array &$post): array
	{
		return [];
	}

	public function validNotEmpty(array &$post, string $name)
	{
		isset($this->_errors) or $this->_errors = [];

		if (! isset($post[$name]) || $post[$name] === '')
		{
			$this->_errors[$name] = 'Введите ' . $name;
		}
	}

	public function validRegex(array &$post, string $name, string $regex)
	{
		isset($this->_errors) or $this->_errors = [];

		if (! isset($post[$name]) || ! preg_match($regex, $post[$name], $match))
		{
			$this->_errors[$name] = "Неправильное $name";
		}

		return $match[1] ?? null;
	}

	public function validIsEmail(array &$post, $name)
	{
		isset($this->_errors) or $this->_errors = [];

		if (! isset($post[$name]) || ($post[$name] = trim($post[$name])) === '')
		{
			$this->_errors[$name] = 'Введите ' . $name;
		}
		elseif (! filter_var($post[$name], FILTER_VALIDATE_EMAIL))
		{
			$this->_errors[$name] = 'Неверный формат ' . $name;
		}
	}

	public function validIsPositiveOrZero(array &$post, $name)
	{
		if (isset($post[$name]) && $post[$name] !== '' && $post[$name] < 0)
		{
			$this->_errors[$name] = "Значение $name неверно";
		}
	}

	public function validIsPositive(array &$post, $name)
	{
		if (isset($post[$name]) && $post[$name] !== '' && $post[$name] <= 0)
		{
			$this->_errors[$name] = "Значение $name неверно";
		}
	}

}
