<?php
session_start();
##### DB Configuration #####
$servername = "localhost";
$username = "root";
$password = "";
$db = "aneeta";
$token="";
##### FB App Configuration #####
$fbPermissions = ['email']; 
$fbappid = "682022782132652"; 
$fbappsecret = "2fa58bee6e54ad34e3929497aa24a938"; 
//$redirectURL = "http://localhost:81/LoginwithFb/authenticate.php"; 
$redirectURL = "http://localhost/anu/authenticate.php/"; 
 
 
##### Create connection #####
$conn = new mysqli($servername, $username, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
##### Required Library #####
require_once __DIR__ . '/src/Facebook/autoload.php';
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
 
$fb = new Facebook(array('app_id' => $fbappid, 'app_secret' => $fbappsecret, 'default_graph_version' => 'v2.6', ));
$helper = $fb->getRedirectLoginHelper();
##### Check facebook token stored or get new access token #####
try {
    if(isset($_SESSION['fb_token'])){
        $accessToken = $_SESSION['fb_token'];
    }else{
        $accessToken = $helper->getAccessToken();
    }
} catch(FacebookResponseException $e) {
    echo 'Facebook Responser error: ' . $e->getMessage();
    exit;
} catch(FacebookSDKException $e) {
    echo 'Facebook SDK error: ' . $e->getMessage();
    exit;
}
?>