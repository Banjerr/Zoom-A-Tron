<?php
require 'vendor/autoload.php';
use sandeepshetty\shopify_api;

session_start(); //start a session

$db = new Mysqli("localhost", "root", "Coramaebanjo1!", "zoomATron");

if($db->connect_errno){
  die('Connect Error: ' . $db->connect_errno);
}

$select_settings = $db->query("SELECT * FROM zoomATronsettings WHERE id = 1");
$app_settings = $select_settings->fetch_object();

if(!empty($_GET['shop'])){ //check if the shop name is passed in the URL
  $shop = $_GET['shop']; //shop-name.myshopify.com

  $select_store = $db->query("SELECT store_name FROM zat_usersettings WHERE store_name = '$shop'"); //check if the store exists

  if($select_store->num_rows > 0){

      if(shopify_api\is_valid_request($_GET, $app_settings->shared_secret)){ //check if its a valid request from Shopify
          $_SESSION['shopify_signature'] = $_GET['signature'];
          $_SESSION['shop'] = $shop;
          header('Location: http://localhost/shopify_testing/admin.php'); //redirect to the admin page
      }

  }else{

      //convert the permissions to an array
      $permissions = json_decode($app_settings->permissions, true);

      //get the permission url
      $permission_url = shopify_api\permission_url(
          $_GET['shop'], $app_settings->api_key, $permissions
      );
      $permission_url .= '&redirect_uri=' . $app_settings->redirect_url;

      header('Location: ' . $permission_url); //redirect to the permission url
  }
}
