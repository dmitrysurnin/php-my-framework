<?php
namespace www;
/** @var $this Mailer */
/** @var $title string */
/** @var $view string */
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?= $title; ?></title>
	</head>
	<body>
	<div style="width: 720px; font-size: 16px; font-family: arial, sans-serif; background-color: white;">
		<?= $view ?>
	</div>
	</body>
</html>
