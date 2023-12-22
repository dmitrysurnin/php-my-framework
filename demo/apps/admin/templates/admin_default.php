<?php
namespace admin;
/** @var $view string */
/** @var $this Controller */
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title><?= $this->title ?></title>
		<link rel="stylesheet" href="/css/libs/bootstrap/bootstrap-2.3.2.css"/>
		<link rel="stylesheet" href="/css/libs/bootstrap/bootstrap-datepicker-1.8.0.min.css">
		<link rel="stylesheet" href="/css/libs/font-awesome/font-awesome-4.7.0.min.css"/>
		<link rel="stylesheet" href="/css/admin_common.css"/>
		<link rel="stylesheet" href="/css/admin_template.css"/>
		<script type="text/javascript" src="/js/libs/jquery/jquery-3.7.1.min.js"></script>
		<script type="text/javascript" src="/js/libs/bootstrap/bootstrap-2.3.2.js"></script>
		<script type="text/javascript" src="/js/libs/bootstrap/bootstrap-datepicker-1.8.0.min.js"></script>
		<script type="text/javascript" src="/js/libs/bootstrap/bootstrap-datepicker.ru-1.8.0.min.js"></script>
		<?php foreach ($this->head as $header) : ?>
			<?php echo $header; ?>
		<?php endforeach; ?>
	</head>
	<body class="<?= $this->action ?>">
		<div class="navbar navbar-default">
			<div class="navbar-inner">
				<a href="http://localhost:8500/" class="brand">
					<i class="fa fa-home"></i>
				</a>
				<div class="nav-collapse collapse">
					<ul class="nav">
						<li class=" dropdown<?= in_array($this->controller, ['users']) ? ' active' : '' ?>">
							<a href="" class="dropdown-toggle" data-toggle="dropdown">Юзеры <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li class="<?= $this->controller == 'users' ? 'active' : '' ?>">
									<a href="/users">Список пользователей</a>
								</li>
							</ul>
						</li>
						<li class=" dropdown<?= in_array($this->controller, ['articles']) ? ' active' : '' ?>">
							<a href="" class="dropdown-toggle" data-toggle="dropdown">Данные <span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li class="<?= $this->controller == 'articles' ? 'active' : '' ?>">
									<a href="/articles">Список статей</a>
								</li>
							</ul>
						</li>
					</ul>
					<ul class="nav pull-right">
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown"><?= $this->session->user_name ?>&nbsp;<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li>
									<a href="http://localhost:8500/auth/logout">Выйти</a>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="container-fluid">
			<?= $view ?>
		</div>
	</body>
</html>
