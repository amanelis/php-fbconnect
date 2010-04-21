<?PHP
//Kill error reporting, just in case, must be on PHP 5
//found some erros in facebook-client/facebook.php that
//in some versions of PHP produce errors, but code will
//work on execution
ini_set("error_reporting", 0);
require_once 'facebook-client/facebook.php'; 

//our key and secret, create new Facebook api object
$appapikey = 'YOURKEY'; 
$appsecret = 'YOURSECRET'; 
$facebook = new Facebook($appapikey, $appsecret);

//This is the main variable that will store the facebook's user id
//important variable you can use throughout your application to reference
//facebook user id
$fb_uid = $facebook->get_loggedin_user();

//only change the exact text 'YOURKEY' with your key, you must leave on the _user
//ex $_COOKIE['208345uhoergu082h4r-92h8_user;]
$fb_user_cookie = $_COOKIE['YOURKEY_user'];

//very important*******
//try to grab facebook friends, if not then there is no user logged in, but cookie is still there
//kill any user cookie by set_user(NULL), this is important, because of facebooks
//cookies, you must set null if there was a recent login on browser.
try {
    $friends = $facebook->api_client->friends_get();
} catch (Exception $e) {
    $facebook->set_user(null, null);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
    <title>Facebook Connect PHP</title>
</head>
<body>
<?
//added cookie as redundancy check
//if fb user is null or cookie is null show login button
if(!$fb_uid || !$fb_user_cookie){
?>
	<div id="user">
		<fb:login-button length="long" onlogin="refresh_page();"></fb:login-button>
	</div>
<?	
} else {
	if(isset($fb_uid)){
		
		//Grab the facebook first_name, last_name.....etc..
		$fb_lname = $facebook->api_client->users_getStandardInfo($fb_uid,'last_name');
		$fb_fname = $facebook->api_client->users_getStandardInfo($fb_uid,'first_name');
		$fb_wname = $facebook->api_client->users_getStandardInfo($fb_uid,'name');
		$fb_uname = $facebook->api_client->users_getStandardInfo($fb_uid,'username');
		$fb_local = $facebook->api_client->users_getStandardInfo($fb_uid,'locale');
		$fb_prurl = $facebook->api_client->users_getStandardInfo($fb_uid,'profile_url');
		$fb_brday = $facebook->api_client->users_getStandardInfo($fb_uid,'birthday');
		$fb_sex   = $facebook->api_client->users_getStandardInfo($fb_uid,'sex');
		$fb_crloc = $facebook->api_client->users_getStandardInfo($fb_uid,'current_location');
		$fb_email = $facebook->api_client->users_getStandardInfo($fb_uid,'proxied_email');	
		
		$f_today = date("Y-m-d");
		$f_ip = $_SERVER['REMOTE_ADDR'];
		
		//This checks to see if this is first time fb users, you should have your db already connected at this point
		//Simply create a table with an 'fb_uid' field, if they are not in database, redirect them to a register page
		//that adds them into database and stores their fb_uid.
		$q_check_user = "SELECT fb_uid FROM fbusers WHERE fb_uid = '$fb_uid'";
		$check_user_result = mysql_query($q_check_user);
		$check_user_num_rows = mysql_num_rows($check_user_result);
		mysql_free_result($check_user_result);		

		//if no matching rows in query, NEW USER, grab values and send to a register form.
		if($check_user_num_rows == 0){
			?>
			<meta http-equiv="REFRESH" content="0;url=register.php?fb_uid=<? echo $fb_uid; ?>">
			<?
		//else, returning facebook user, show their information
		}else {
			echo "Welcome, <a href=\"http://www.facebook.com/profile.php?id=$fb_uid\">".$fb_fname[0]['first_name']." ".$fb_lname[0]['last_name']."</a>.<br>";
			echo "You are signed in with your Facebook account.<br>";
			?>
			<br>
			<a href="#" onClick="FB.Connect.logoutAndRedirect('/connect.php')">Logout</a>
			<?
		}
	//if enters into this else statement, there is a serious problem.....
	} else {
		echo "Please clear your cookies in your browser and login again if you see this!<br>";	
	}
}
?>
<script type="text/javascript" src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php">
</script>
<script type="text/javascript">
function refresh_page(){
	window.location.reload();
}
FB.init("YOURKEY","xd_receiver.htm");
</script>
</body>
</html>
