<?php
/** @var BasePage $this */
?>
<!DOCTYPE html><html>
<head>
	<meta http-equiv=Content-Type content="text/html;charset=utf-8">
	<title><?=$this->title?></title>
    <?php //$this->loadCSS() ?>
    <?php //$this->loadJS() ?>
</head>
<body>
<div id="main">
	<?php
	$this->showBeforeBody();
	?>

	<?php
	$this->showContent();
	?>
    <?php $this->loadJSBottom() ?>
	<?php
	$this->showAfterBody();
	?>
</body>
</html>