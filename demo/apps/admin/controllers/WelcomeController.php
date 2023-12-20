<?php
namespace admin;

class WelcomeController extends Controller
{
	public function actionIndex()
	{
		$this->renderData('index');
	}

}
