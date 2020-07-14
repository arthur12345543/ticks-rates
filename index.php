<?php
require_once("sess.php");
require __DIR__ . '/vendor/autoload.php';
require_once("libs/lib_meths.php");
$lib_meths = new lib_meths;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Rate it today</title>
	<meta name="description" content="Some lorem ipsum tick rates task">
	<meta name="keywords" content="tick, rates">
	<meta name="author" content="Rate it today">
	<link href="css/jquery-ui.min.css" rel="stylesheet">
	<link href="css/main.css?v=18" rel="stylesheet">
</head>
<body>
	<div class="nav">
		<a href="/"><div class="logo-title">Check Rates for USD</div></a>
	</div>
	<div class="f-rate">
		<input type="hidden" name="csrf" class="csrf" value="<?php echo $csrf ?>">
		<input type="text" name="amount-usd" class="amount-usd" placeholder="1 USD">
		<div class="append-generated-select"></div>
		<button>Convert</button>
		<div class="response"></div>
	</div>
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/SmoothScroll.js"></script>
	<script src="js/main.js?v=4"></script>
</body>
</html>