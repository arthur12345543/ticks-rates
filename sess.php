<?php
session_start();
header('Cache-Control: max-age=84600');
if(empty($_SESSION['key'])){
	$_SESSION['key'] = bin2hex(random_bytes(32));
}
$csrf = hash_hmac('sha256', 'hA19*//1@$AA>?KDjaWj', $_SESSION['key']);
/*

if non random bytes in PHP 

function random_bytes($length = 6){
    $characters = '0123456789';
    $characters_length = strlen($characters);
    $output = '';
    for ($i = 0; $i < $length; $i++)
        $output .= $characters[rand(0, $characters_length - 1)];

    return $output;
}
*/
?>