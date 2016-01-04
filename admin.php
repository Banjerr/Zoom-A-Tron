<?php
session_start();

require __DIR__.'/vendor/autoload.php';
use phpish\shopify;

require __DIR__.'/conf.php';	session_start();

	require __DIR__.'/vendor/autoload.php';
	use phpish\shopify;

	require __DIR__.'/conf.php';

# shopify\access_token can throw an exception
$oauth_token = shopify\access_token($_GET['shop'], SHOPIFY_APP_API_KEY, SHOPIFY_APP_SHARED_SECRET, $_GET['code']);

$_SESSION['oauth_token'] = $oauth_token;
$_SESSION['shop'] = $_GET['shop'];

echo 'App Successfully Installed!';
