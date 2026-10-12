<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('sessionKey.php');


function getConnection(){
	require('db_connection.php');

	if (!isset($connection) || !($connection instanceof mysqli)){
		throw new Exception("Database cannot connect");
	}

	return $connection;
}

function doRegister($username, $email, $password){
	$username = trim($username);
	$email = trim($email);

	//validate again for security reasons, someone could bypass through the frontend so this enforces the data to be in correct format
	if (
		$username === "" ||
		!filter_var($email, FILTER_VALIDATE_EMAIL) ||
		!preg_match('/[A-Z]/', $password) ||
		!preg_match('/[a-z]/', $password) ||
		!preg_match('/[0-9]/', $password)
	) {
		return ["success" => false, "message" => "Invalid registration information"];
	}

	$connection = getConnection();

	//We're gonna check here if the user exists already
	$stmt = $connection->prepare(
		"SELECT uid FROM users
		WHERE username = ? or email = ?"
	);

	if ($stmt->num_rows > 0){
		$stmt->close();

		return ["success" => false, "message" => "Username or email already exists"];
	}

	$stmt->close();

	$hashedPw = password_hash($password, PASSWORD_DEFAULT);

	//Once it passes that test, we're going to put it into the Users table
	$stmt = $connection->prepare(
		"INSERT INTO users (username, email, password_hash)
		VALUES (?, ?, ?)"
	);

	$stmt->bind_param("sss", $username, $email, $hashedPw);

	$success = $stmt->execute();
	$stmt->close();

	//Boom, now it goes into rabbitmq
	return [
		"success" => $success,
		"message" => $success
			? "Registration successful"
			: "Registration failed"
	];

}

function doLogin($username,$password)
{
	$connection = getConnection();
// checing if the user a nd password hash exist in the table
	$stmt = $connection->prepare(
		"SELECT uid, password_hash
		FROM users
		WHERE username = ? OR email = ?
		LIMIT 1"
	);
	
	//binding the parameters
    $stmt->bind_param("ss", $username, $username);
    //executing query
    $stmt->execute();
	
	// getting results from the queryy, hoping this works
	$stmt_result = $stmt->get_result();

	$stmt->close();

	//fetch associative array
	if ($row = $stmt_result->fetch_assoc()) {
		// if the useer exists, getting the uid and pwd hash from users table
		$uid = $row['uid'];
		$hashedPw = $row['password_hash'];

	//use password_verify($password, $hashedPW), returns true when entered password matches the stored hash
	$verify_pwd =password_verify($password, $hashedPw);
	if ($verify_pwd) 
		{
		//generate session token after successful login
		$session_token = keyGenerator($uid);
		//hash the token
		$token_hash = hash('sha256', $session_token);
		//insert the token in the able with expiry
		$stmt = $connection->prepare (
			"INSERT INTO sessions (uid, session_token, created_at, expires_time)
			VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 30 MINUTE))"
		);
		//bind params
		$stmt->bind_param("is", $uid, $token_hash);
	// run the query
		$stmt->execute();
		// closing the query
		$stmt->close();
		//return the token to the frontend
		return
		[
			//login successful, geerate session token for frontend
			'success' => true,
			'session_token' => $session_token,
			'user_id' => $uid,
			'message' => 'Login successful'
		];
		} 
	}
	return 
	[
		// no session token generated if login credentials are wrong
		//login failed
		'success' => false,
		'message' => 'Login unsuccessful'
	];
	
}

function doValidate($sessionToken){
	//hash the incoming token
	$token_hash = hash('sha256', $sessionToken);
	// get connection to the database
	$connection = getConnection();
	//check token exists in table and is not expired
	$stmt = $connection->prepare(
		"SELECT uid
		FROM sessions
		WHERE session_token = ? AND expires_time > NOW()
		LIMIT 1"
	);
	//bind the parameters
	$stmt->bind_param("s", $token_hash);
	//execute the query
	$stmt->execute();
	/// get the result of the query
	$stmt_result = $stmt->get_result();
	// close the query
	$stmt->close();
	// from result fetch associative array
	if ($row = $stmt_result->fetch_assoc()) {
		return [
			// session valid, return user id to front end
			"success" => true,
			"message" => "Session is valid",
			"user_id" => $row['uid']
		];
	}

	return [
		//session invalid , nothing goes to front end
		"success" => false,
		"message" => "Invalid or expired session"
	];

}

function doLogout($sessionToken){
	//hash the incoming token
	$token_hash = hash('sha256', $sessionToken);
	// get connection to the database
	$connection = getConnection();
	//delete the session token from session table
		$stmt = $connection->prepare(
			"DELETE FROM sessions WHERE session_token = ?"
		);
		// bind the param
		$stmt->bind_param("s", $token_hash);
		// run the query
		$stmt->execute();
		//close query
		$stmt->close();
	//return response
	return [
		// let user  logout was successful
		"success" => true,
		"message" => "Logout successful"
	];
		
	}


function requestProcessor($request){
	//check if we got a good request type
	if (!is_array($request) || !isset($request["type"])){
		return ["success" => false, "message" => "Invalid request type"];
	}

	echo "Received request: " . $request["type"] . PHP_EOL;

	try {
		switch($request["type"]){
			//gonna use register to explain the flow of this thing since its what I made
			//register sends username,email,password to rabbitmq, requestProcessor detects the type then calls the associated function, in this case doRegister
			//doRegister will validate the input, connect to MySQL, check for dupes, hash the password, then insert the new user
			//success is then true if it all works, false and displays an error. The rests are basically do the exact same flow with their own little twist.
			case "register":
				return doRegister(
					$request["username"] ?? "",
					$request["email"] ?? "",
					$request["password"] ?? ""
				);
			case "login":
				return doLogin(
					$request["username"] ?? "",
					$request["password"] ?? ""
				);
			case "validate_session":
				return doValidate($request["sessionToken"] ?? $request["session_token"] ?? "");
			case "logout":
				return doLogout($request["sessionToken"] ?? $request["session_token"] ?? "");
			default:
				return [
					"success" => false,
					"message" => "No idea what this request type is"
				];
			//if you notice that i'm returning instead of just breaking
			//return passes our results back to the rabbitmq lib. if we only call the fucntion without returning,
			//then our frontend wouldnt receive any success responses
		}
	} catch (Throwable $e) {
		//should always catch errors/exceptions, you never know what can get through
		//One example could be when MySQL just isnt working, itll tell us instead of saying nothing
		error_log("Listener error: " . $e->getMessage());

		return [
			"success" => false,
			"message" => "Internal server error"
			];
	}
}
// NOW WE START IT!!!!!!!!!!!
$server = new rabbitMQServer(
	"testRabbitMQ.ini",
	"testServer"
);

echo "Database listener started..." . PHP_EOL;

$server->process_requests("requestProcessor");
?>
