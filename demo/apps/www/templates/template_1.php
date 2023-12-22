<?php
namespace www;
/** @var $this Controller */
/** @var $view string */
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<title><?= $this->title ?></title>
		<link rel="stylesheet" href="/css/common.css"/>
		<link rel="stylesheet" href="/css/template_1.css"/>
		<?php foreach ($this->head as $head): ?>
			<?php echo $head; ?>
		<?php endforeach; ?>
		<script type="text/javascript" src="/js/libs/jquery/jquery-3.7.1.min.js"></script>
		<meta name="description" content="<?= $this->description ?>">
		<meta name="keywords" content="<?= $this->keywords ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	</head>
	<body>
		<div id="wrapper">
			<header>
				<div id="logo">
					<a href="/" class="logo-main<?= $this->controller == 'welcome' ? ' active' : '' ?>">
						<img src="/images/logo.png">
						<span>Имя Сайта</span>
					</a>
				</div>
				<div id="nav-right">
					<?php if ($this->session->user_id) : ?>
						<?php if ($this->session->user_is_admin) : ?>
							<a href="http://localhost:8501/" style="color:red">admin</a>
						<?php endif?>
						<a href="/auth/logout">выйти</a>
					<?php else : ?>
						<a href="/auth"<?= $this->controller == 'auth' ? ' class="active"' : '' ?>>войти</a>
					<?php endif?>
				</div>
			</header>
			<div id="content" class="<?= $this->controller . ' ' . $this->action ?>">
				<?= $view ?>
			</div>
			<footer>
				<span class="copyright">Мой Сайт <?= date('Y') ?></span>
			</footer>
		</div>
	</body>
</html>
