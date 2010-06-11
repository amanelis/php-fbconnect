<?php
require_once 'facebook-client/facebook.php'; 
require_once 'facebook-client/facebookapi_php5_restlib.php'; 
$appapikey = '{YOUR API KEY HERE}'; 
$appsecret = '{YOUR APPLICATION SECRET KEY HERE}'; // do this after validation 

$userid = $_REQUEST['fb_sig_user']; 
$sKey = $_REQUEST['fb_sig_session_key']; 

if($_REQUEST['action'] == "getName") { 
	try { $fbClient = new FacebookRestClient($appapikey, $appsecret, $sKey); 
	} catch(FacebookRestClientException $ex) 
		{ 
			// Handle Exception 
		}
$userInfo = $fbClient->fql_query("SELECT name FROM user WHERE uid=$userid"); 
$userName = $userInfo[0]['name']; 

// $userName now contains user's name. 

}
?>
