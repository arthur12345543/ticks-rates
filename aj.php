<?php
require_once("sess.php");
require __DIR__ . '/vendor/autoload.php';
require_once("libs/lib_meths.php");
$lib_meths = new lib_meths;

use \BenMajor\ExchangeRatesAPI\ExchangeRatesAPI;
use \BenMajor\ExchangeRatesAPI\Response;
use \BenMajor\ExchangeRatesAPI\Exception;

$currencies = array("AUD","BRL","GBP","BGN","CAD","CNY","HRK","CZK","DKK","EUR","HKD","HUF","ISK","IDR","INR","ILS","JPY","MYR","MXN","NZD","NOK","PHP","PLN","RON","RUB","SGD","ZAR","KRW","SEK","CHF","THB","TRY","USD");

if(isset($_POST['curr'])){
	$curr = $lib_meths->valid_data($_POST['curr']);
	$amount = $lib_meths->valid_data($_POST['amount']);
	$csrf = $lib_meths->valid_data($_POST['csrf']);
	if($amount=="" || $amount==null){
		$amount = 1;
	}
	if(!hash_equals($csrf, $_POST['csrf'])){
		die("Error");
	}
	
	$lookup = new ExchangeRatesAPI();
	$rates  = $lookup->addRate($curr)->setBaseCurrency('USD')->fetch();
	$response = $rates->getStatusCode();
	
	if($response!=200){
		die("Error");
	}

	if(!in_array($curr,$currencies)){
		die("Error");
	}

	if(!is_numeric($amount)){
		die("Error");
	}

	$tich_rate = $rates->getRates()[$curr];
	$rate_convert = $tich_rate*$amount;
	echo "$amount USD costs $rate_convert $curr<br> 1 USD is $tich_rate $curr";
}else{
	die("Error");
}
?>