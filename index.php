<?php
require_once 'vendor/autoload.php';
use phpish\shopify;

session_start(); //start a session

$db = new mysqli("localhost", "admin", "Coramaebanjo1!", "zoomATron");
if ($db->connect_errno) {
    echo "Failed to connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error;
}

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
          header('Location: http://zoomatron.countryfriedcoders.me/admin.php'); //redirect to the admin page
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

if(!empty($_GET['shop']) && !empty($_GET['code'])){

  $shop = $_GET['shop']; //shop name

  //get permanent access token
  $access_token = shopify_api\oauth_access_token(
      $_GET['shop'], $app_settings->api_key, $app_settings->shared_secret, $_GET['code']
  );

  //save the shop details to the database
  $db->query("
     INSERT INTO zat_usersettings
     SET access_token = '$access_token',
     store_name = '$shop'
 ");

  //save the signature and shop name to the current session
  $_SESSION['shopify_signature'] = $_GET['signature'];
  $_SESSION['shop'] = $shop;

  header('Location: http://zoomatron.countryfriedcoders.me/admin.php');
}
