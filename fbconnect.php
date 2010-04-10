<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<?PHP
//Kill error reporting, just in case...
ini_set("error_reporting",0);
require_once 'facebook-client/facebook.php'; 

//our key and secret, create new Facebook api object
$appapikey = 'YOURKEY'; 
$appsecret = 'YOURSECRET'; 
$facebook = new Facebook($appapikey, $appsecret);
$fb_uid = $facebook->get_loggedin_user();
$fb_user_cookie = $_COOKIE['YOURKEY_user'];

//very important*******
//try to grab facebook friends, if not then there is no user logged in
//kill any user cookie by set_user(NULL)
try {
    $friends = $facebook->api_client->friends_get();
} catch (Exception $e) {
    $facebook->set_user(null, null);
}

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
		
		//Parse any arrays, store values, prepare for auto form submission if new user
		$f_lname = $fb_lname[0]['last_name'];
		$f_fname = $fb_fname[0]['first_name'];
		$f_wname = $fb_wname[0]['name'];
		$f_uname = $fb_uname[0]['username'];
		$f_local = $fb_local[0]['locale'];
		$f_prurl = $fb_prurl[0]['profile_url'];
		$f_brday = $fb_brday[0]['birthday'];
		$f_sex	 = $fb_sex[0]['sex'];
		$f_email = $fb_email[0]['proxied_email'];
		$f_city  = $fb_crloc[0]['current_location']['city'];
		$f_state = $fb_crloc[0]['current_location']['state'];
		$f_crtny = $fb_crloc[0]['current_location']['country'];
		$f_zipco = $fb_crloc[0]['current_location']['zip'];
		
		$f_today = date("Y-m-d");
		$f_ip = $_SERVER['REMOTE_ADDR'];
		
		//This checks to see if this is first time fb users, you should have your db already connected at this point
		$q_check_user = "SELECT fb_uid, school, logins, plogin, points FROM fbusers WHERE fb_uid = '$fb_uid'";
		$check_user_result = mysql_query($q_check_user);
		$check_user_num_rows = mysql_num_rows($check_user_result);
		mysql_free_result($check_user_result);		

		//if no matching rows in query, NEW USER, grab values and send to a register form.
		if($check_user_num_rows == 0 ){
			?>
			<meta http-equiv="REFRESH" content="0;url=register.php?fb_uid=<? echo $fb_uid; ?>">
			<?
		//else, returning facebook user, show their information
		}else {
			echo "Welcome, <a href=\"http://www.facebook.com/profile.php?id=$fb_uid\">". $fb_fname[0]['first_name']." 
			".$fb_lname[0]['last_name']."</a>.<br>";
			echo "You are signed in with your Facebook account.<br>";
			?>
			<br>
			<a href="#" onClick="FB.Connect.logoutAndRedirect('/index.php')">Logout</a>
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
