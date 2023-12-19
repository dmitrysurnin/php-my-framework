<?php
namespace myframework;

/**
 * @property CWebApplication $app
 */
trait CRender
{
	/**
	 * с template и view
	 */
	public function renderPage(string $_template_, string $_view_, array $_data_ = [], bool $_return_ = false): ?string
	{
		extract($_data_);

		$_viewFile_ = APP . '/views/' . $_view_ . '.php';

		ob_start();

		require $_viewFile_;

		$view = ob_get_contents(); /// используется в $_templateFile_

		ob_clean();

		$_templateFile_ = APP . '/templates/' . $_template_ . '.php';

		require $_templateFile_;

		$_page_ = ob_get_contents();

		ob_end_clean();

		if (! $_return_)
		{
			echo $_page_;

			return null;
		}
		else
		{
			return $_page_;
		}
	}

	/**
	 * view без template
	 */
	protected function render(string $_view_, array $_data_ = [], bool $_return_ = false): ?string
	{
		extract($_data_);

		substr($_view_, 0, 1) !== '/' and $_view_ = APP . '/views/' . $_view_;

		$_viewFile_ = $_view_ . '.php';

		if (! $_return_)
		{
			require $_viewFile_;
		}
		else
		{
			ob_start();

			require $_viewFile_;

			$_view_ = ob_get_contents();

			ob_end_clean();

			return $_view_;
		}

		return null;
	}

	/**
	 * view без template - в случае ajax-запроса
	 * с template и view - в случае обычного запроса
	 */
	protected function renderData(string $_template_, string $_view_, array $_data_ = [], bool $_return_ = false): ?string
	{
		if (F::$app->request->isAjax)
		{
			return $this->render($_view_, $_data_, $_return_);
		}
		else
		{
			return $this->renderPage($_template_, $_view_, $_data_, $_return_);
		}
	}

}
