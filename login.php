<?php
require_once ('path.inc');
require_once ('get_host_info.inc');
require_once('rabbitMQLib.inc');

$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? "");
    $password = $_POST['password'] ?? "";

    //check if the username and password are empty
    if ($username ==="") 
        {
            $message = "Please enter your username or email.";
        }
    elseif ($password === "")
        {
            $message = "Please enter your password.";
        }

    else 
        {
            // conecting to rabbitmq
            $client = new rabbitMQClient
            ("testRabbitMQ.ini", "testServer");

        // LOGIN REQUEST
        $request = array();
        $request['type'] = "login";
        $request['username'] = $username;
        $request['password'] = $password;
        
    //send request to rabbitmq and wait for a repky from listner
        $response = $client->send_request($request);
        // check if login was successful and includes a token
        if (isset($response["success"]) && $response["success"] === true 
        && isset ($response['session_token']) )
        {
        // storing token in cookie for 30 min and going to home page & works on http only
        setcookie("sessionToken", 
        $response['session_token'], 
        ['expires' =>time() + 1800, 
        'path' => "/" , 
        'httponly' => true]
        );

        // let user accesss login after successful login
        header("Location: home.php");
        exit();
        }
        else
            {
                $message = $response["message"] ?? "Login failed.";
            }
        }

}
if($message !== "") {
    echo "<p>" .htmlspecialchars($message) . "</p>";
}
?>
<!DOCTYPE html>

<h3>Login</h3>
<form method="POST">
<div>
    <!-- enter the email or username -->
    <label for="username">Email or Username</label><br>
    <input type="text" id="username" name="username" required><br>
    
    <!-- enter the password -->    
    <label for="pwd">Password</label><br>
    <input type="password" id="pwd" name="password" required><br>

    <!-- adding button for login -->
    <button type="submit">Login</button>
</div>
</form>

<br>

<!-- link to registration page -->
 <a href="register.php">Don't have an account? Register here</a>

