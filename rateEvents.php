<?php
chdir(__DIR__ . '/..');
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

?>
<head>
<title>Rate Page</title>
<script>
async function validateSession() {	
	const sessionToken = sessionStorage.getItem('session_token');
	const response = await fetch("sessionValidator.php", {
            method: "POST",
            headers:{
            //header tells the server the format of the request's body
            "Content-Type": "application/json"},
            // converts the login id and password into json to use as request body
            body: JSON.stringify({typeRequest:'validate_session', session_token: sessionToken})
        });
        // waits for the server reply 
        const data = await response.json();

        //checks whether the session key is valid
        if (!data.success)
        {
            //else returns user back to login page
            window.location.href = "login.php";
            
	}
}
validateSession();

async function loadEvents(){
	const response = await fetch("get_events_handler.php");
	const data = await response.json();
	
			console.log(data);
	if (data.success){
		for (const event of data.events){
			console.log(event.EventName, event.City);
		}
	}
}
loadEvents();
</script>
</head>

<body>
<h1>Rate up coming events!<h1>

<a href="home.php">Home</a>
</body>
