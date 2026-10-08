<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

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
		"SELECT UserID FROM Users
		WHERE Username = ? or Email = ?"
	);

	if ($stmt->num_rows > 0){
		$stmt->close();

		return ["success" => false, "message" => "Username or email already exists"];
	}

	$stmt->close();

	$hashedPw = password_hash($password, PASSWORD_DEFAULT);

	//Once it passes that test, we're going to put it into the Users table
	$stmt = $connection->prepare(
		"INSERT INTO Users (Username, Email, Password)
		VALUES (?, ?, ?)"
	);

	$stmt->bind_param("sss", $username, $email, $hasedPw);

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

function doLogin($loginID,$password)
{
	$connection = getConnection();

	$stmt = $connection->preprare(
		"SELECT UserID, Password
		FROM Users
		WHERE Username = ? OR Email = ?
		LIMIT 1"
	);
	// do the password hash
	//use password_verify($password, $hashedPW), returns true when entered password matches the stored hash
	//generate session token after successful login
	//return the token to the frontend
    //creating a prepared stament to look user id
    $stmt = $connection->prepare("SELECT UserID FROM Users Where UserName =? or Email=?");
    //binding the parameters
    $stmt->bind_param("ss", $users);
    //executing query
    $stmt->execute();
  //binding result variables
    $stmt->bind_result($loginID);
  // fetching values
    $stmt->fetch();
    // check password
    return true;
    //return false if not valid
}

function doValidate($sessionID){
	//receive the session token
	//hash the incoming token
	//check whether the token exists and hasnt expired
	//return the validation results
}

function doLogout($sessionID){
	//same thing as doValidate
	//delete the session from MySQL
	//return response
	//remove the token from session storage or wherever joseph put it
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
				return doValidate($request["sessionID"] ?? $request["session_token"] ?? "");
			case "logout":
				return doLogout($request["sessionID"] ?? $request["session_token"] ?? "");
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
