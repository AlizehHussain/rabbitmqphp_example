<?php
chdir(__DIR__ . '/..');
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
?>

<h1><em>Logging out...</em></h1>

<script>
async function destroySession() {	
	const sessionToken = sessionStorage.getItem('session_token');
	const response = await fetch("sessionValidator.php", {
            method: "POST",
            headers:{
            //header tells the server the format of the request's body
            "Content-Type": "application/json"},
            // converts the login id and password into json to use as request body
            body: JSON.stringify({typeRequest:'logout', session_token: sessionToken})
        });
        // waits for the server reply 
        const data = await response.json();

        //checks whether the session key is valid
        if (data.success)
        {
            //else returns user back to login page
            window.location.href = "login.php";
            
	}
}
destroySession();
</script>

