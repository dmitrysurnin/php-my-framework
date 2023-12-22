<?php
namespace www;

class WelcomeController extends Controller
{
	public function actionIndex()
	{
		$this->renderData('index', [
		]);
	}

}
