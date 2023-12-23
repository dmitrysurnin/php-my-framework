<?php
namespace myframework;

class CAutoLoader
{
	protected static array $_coreClasses = [
		'myframework\CAdminController' => '/ccontroller/CAdminController.php',
		'myframework\CApplication' => '/base/CApplication.php',
		'myframework\CAssertException' => '/errors/CAssertException.php',
		'myframework\CAuth' => '/auth/CAuth.php',
		'myframework\CAuthShort' => '/auth/CAuthShort.php',
		'myframework\CAutoLoader' => '/classes/CAutoLoader.php',
		'myframework\CCaptcha' => '/auth/CCaptcha.php',
		'myframework\CConsoleApplication' => '/console/CConsoleApplication.php',
		'myframework\CConsoleCommand' => '/console/CConsoleCommand.php',
		'myframework\CConsoleCommandRunner' => '/console/CConsoleCommandRunner.php',
		'myframework\CController' => '/ccontroller/CController.php',
		'myframework\CControllerHtmlInputs' => '/ccontroller/CControllerHtmlInputs.php',
		'myframework\CErrorHandler' => '/errors/CErrorHandler.php',
		'myframework\CException' => '/errors/CException.php',
		'myframework\CHttpException' => '/errors/CHttpException.php',
		'myframework\CHttpSession' => '/base/CHttpSession.php',
		'myframework\CHttpSessionHandler' => '/base/CHttpSessionHandler.php',
		'myframework\CMailer' => '/components/CMailer.php',
		'myframework\CModel' => '/cmodel/CModel.php',
		'myframework\CModelApplyFilters' => '/cmodel/CModelApplyFilters.php',
		'myframework\CModelDataBuild' => '/cmodel/CModelDataBuild.php',
		'myframework\CModelDataCollect' => '/cmodel/CModelDataCollect.php',
		'myframework\CModelPrivateCommandCreators' => '/cmodel/CModelPrivateCommandCreators.php',
		'myframework\CModelPrivateFetchers' => '/cmodel/CModelPrivateFetchers.php',
		'myframework\CModelPublicFinders' => '/cmodel/CModelPublicFinders.php',
		'myframework\CModelPublicUpdateDB' => '/cmodel/CModelPublicUpdateDB.php',
		'myframework\CModelValidate' => '/cmodel/CModelValidate.php',
		'myframework\CMysqlConnector' => '/db/mysql/CMysqlConnector.php',
		'myframework\CMysqlInstance' => '/db/mysql/CMysqlInstance.php',
		'myframework\CMysqlQuery' => '/db/mysql/CMysqlQuery.php',
		'myframework\CMysqlTransaction' => '/db/mysql/CMysqlTransaction.php',
		'myframework\CRender' => '/traits/CRender.php',
		'myframework\CSingleton' => '/base/CSingleton.php',
		'myframework\CWebApplication' => '/base/CWebApplication.php',
		'myframework\CWebRequest' => '/base/CWebRequest.php',
		'myframework\F' => '/base/F.php',
	];

	public static function autoload($className)
	{
		if (isset(self::$_coreClasses[$className]))
		{
			require_once F_ROOT . self::$_coreClasses[$className];
		}
		elseif (isset(F::$app->classesList[$className]))
		{
			require_once F::$app->classesList[$className];
		}
		else
		{
			include_once $className . '.php';
		}
	}

}
