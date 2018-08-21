<?php 
require_once 'Config.php';
if(isset($_REQUEST['code'])){
    header('Location: authenticate.php');
}
############ Set Fb access token ############
if(isset($_SESSION['fb_token'])){
        $fb->setDefaultAccessToken($_SESSION['fb_token']);
}
else{
    $_SESSION['fb_token'] = (string) $accessToken;
    $oAuth2Client = $fb->getOAuth2Client();
    $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['fb_token']);
    $_SESSION['fb_token'] = (string) $longLivedAccessToken;
    $fb->setDefaultAccessToken($_SESSION['fb_token']);
}
############ Fetch data from graph api  ############
try {
    $profileRequest = $fb->get('/me?fields=name,first_name,last_name,email,link,gender,locale,birthday,cover,picture.type(large)');
    $fbUserProfile = $profileRequest->getGraphNode()->asArray();
} catch(FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    session_destroy();
    header("Location: ./");
    exit;
} catch(FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    session_destroy();
    header("Location: ./");
    exit;
}
############ Store data in database  ############
$oauthpro = "facebook";
$oauthid = $fbUserProfile['id'];
$f_name = $fbUserProfile['first_name'];
$l_name = $fbUserProfile['last_name'];
$gender = $fbUserProfile['gender'];
$email_id = $fbUserProfile['email'];
$locale = $fbUserProfile['locale'];
$cover = $fbUserProfile['cover']['source'];
$picture = $fbUserProfile['picture']['url'];
$url = $fbUserProfile['link'];
$sql = "SELECT * FROM usersdata WHERE oauthid='".$fbUserProfile['id']."'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
   $conn->query("update usersdata set f_name='".$f_name."', l_name='".$l_name."', email_id='".$email_id."', gender='".$gender."', locale='".$locale."', cover='".$cover."', picture='".$picture."', url='".$url."' where oauthid='".$oauthid."' ");
} else {
    $conn->query("INSERT INTO usersdata (oauth_pro, oauthid, f_name, l_name, email_id, gender, locale, cover, picture, url) VALUES ('".$oauthpro."', '".$oauthid."', '".$f_name."', '".$l_name."', '".$email_id."', '".$gender."', '".$locale."', '".$cover."', '".$picture."', '".$url."')");  
}
$res = $conn->query($sql);
$userData = $res->fetch_assoc();
 
$_SESSION['userData'] = $userData;
header("Location: view.php");
?>