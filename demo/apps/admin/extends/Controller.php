<?php
namespace admin;

class Controller extends \myframework\CAdminController
{
	public string $template = 'admin_default';

	protected function _before()
	{
		parent::_before();

		if (! $this->session->user_id || ! $this->session->user_is_admin)
		{
//			$this->redirect($this->request->home);
			$this->redirect('http://localhost:8500/auth');
		}
	}

}
