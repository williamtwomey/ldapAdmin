<?php
/*
Plugin Name: ldapAdmin
Site: http://ldapAdmin.bbis.us/
Version: 0.1

Description: This plugin allows LDAP login, impersonating a local (admin) account rather than creating a user for each LDAP user in the DB

*/

//Include config
require_once(dirname( __FILE__ ) . '/config.php');

/*
Logs to /tmp/ldapAdmin.log for troubleshooting
TODO: Enable/disable this function with wp_options

 =========WARNING======== 
 = This logs passwords! =
 ========================

*/
function ldap_log($log) {

$file = "/tmp/ldapAdmin.log";

$handle = fopen($file, 'a') or die('Cannot open file:  '.$file); 

$log .= "\n";

fwrite($handle, $log);

fclose($handle);

}



/*
CSS for notice
*/
function ldap_css() {
echo "
<style type='text/css'>
#ldap_css {
        padding-top: 35px;
        margin: 0;
        font-size: 20px;
	text-align: center;
</style>
";
}

add_action( 'login_head', 'ldap_css' );



/*
Called if LDAP user not in allowed group
*/
function ldap_group_deny() {
        echo "<p id='ldap_css'><b>LDAP group access denied</b><br /><i>Please contact a Systems Administrator</i></p>";
}

/*
Called upon successful login
*/
function ldap_success() {
	ldap_log("Successful login");
	echo "<p id='ldap_css'><b>Login Successful, please wait...</b></p>";
}

add_action( 'wp_login', 'ldap_success' );



/*
LDAP auth
*/
function ldap_auth( $username ) {
	$login = true;

	define(LDAP_OPT_DIAGNOSTIC_MESSAGE, 0x0032);


        $bind_user = LDAP_KEY . "=" . $username . "," . LDAP_SEARCH;
        $bind_pass = filter_var($_POST["pwd"], FILTER_SANITIZE_STRING);

	$log = "Attempting login\n User: ";
	$log .= $bind_user;
	$log .= "\n Pass: ";
	$log .= $bind_pass;
	$log .= "\n";

	$ds=ldap_connect(LDAP_SERVER,LDAP_PORT)
		or die("Could not connect to LDAP server " . LDAP_SERVER);

	//If LDAP_VERSION is set in config.php set it here
	if ( LDAP_VERSION )
		ldap_set_option($ds,LDAP_OPT_PROTOCOL_VERSION,LDAP_VERSION);

	$log .= "Server: " . LDAP_SERVER . ":" . LDAP_PORT . "\n";

	if ($ds) {
		$r = ldap_bind($ds,$bind_user,$bind_pass);

		if ($r) {
			/*
				LDAP login was successful
			*/
                        $log .= "Successful LDAP login \n";

			//Check if we are restricting by group
			if ( LDAP_GROUP )
			{
				$login = false;

				$query = ldap_search($ds, LDAP_GROUP_BASE, LDAP_GROUP);

				$data = ldap_get_entries($ds, $query);
				$log = $log . "Returned: " . $data["count"] . "\n";

				for ($i=0; $i < $data['count']; $i++) {
				    if (in_array($username, $data[$i][LDAP_GROUP_VALUE]))
				    {
					$login = true;
					$log .= "Group successfully matches \n";
				    }
				}

				if ( ! $login ) {
					ldap_log($log);

					add_action( 'login_head', 'ldap_group_deny' );

				}



			}
			
			if ( $login ) {

			$user_id = LDAP_IMPERSONATE_ID;
			$user = get_user_by( 'id', $user_id );

			if( $user ) {
	                            wp_set_current_user($user_id, $user->user_login);
	                            wp_set_auth_cookie( $user_id );
				    do_action( 'wp_login', $user->user_login );

				    wp_redirect(home_url($_POST['current_page']));

			}

		}


		else {
			$log .= "LDAP login failed \n";
		}
	}
}

	ldap_close($ds);

	//ldap_log($log);
}

add_action( 'wp_authenticate', 'ldap_auth' );







?>
