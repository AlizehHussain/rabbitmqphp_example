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

function doSyncEvents($events){
	if (!is_array($events) || empty($events)){
		return [
			"success" => false,
			"message" => "No valid events received"
		];
	}

	$connection = getConnection();

	try {
		$connection->begin_transaction(); //starting a transaction so the entire batch succeeds or fails

		$stmt = $connection->prepare(
			"INSERT INTO Events (EventID, EventName, EventDate, Venue, City, Category, TicketURL)
			VALUES (?, ?, ?, ?, ?, ?, ?)
			ON DUPLICATE KEY UPDATE
				EventName = VALUES(EventName),
				EventDate = VALUES(EventDate),
				Venue = VALUES(Venue),
				City = VALUES(City),
				Category = VALUES(Category),
				TicketURL = VALUES(TicketURL)"
		);

		$count = 0;

		foreach ($events as $event){
			if(!is_array($events) || empty($event["event_id"]) || empty($event["name"])){
				throw new Exception("Invalid event information");
			}

			$eventID = $event["event_id"];
			$eventName = $event["name"];
			$eventDate = $event["date"] ?? null;
			$venue = $event["venue"] ?? "";
			$city = $event["city"] ?? "";
			$category = $event["category"] ?? "";
			$ticketURL = $event["url"] ?? "";

			$stmt->bind_param(
				"sssssss",
				$eventID,
				$eventName,
				$eventDate,
				$venue,
				$city,
				$category,
				$ticketURL
			);

			$stmt->execute();
			$count++;
		}

		$stmt->close();

		$connection->commit();

		return [
			"success" => true,
			"message" => "Events synchronized successfully",
			"count" => $count
		];
	} catch (Throwable $e){
		$connection->rollback(); //undo the entire batch if something fails

		error_log("Event sync error: " . $e->getMessage());

		return [
			"success" => false,
			"message" => "Failed to synchronize events"
		];
	} finally {
		$connection->close();
	}
}

//turns a  dtatabse row into the same field names the DMZ script already uses
function formatEvent($row){
return [
	"event_id" => $row["EventID"],
	"name" => $row["EventName"],
	"date" => $row["EventDate"],
	"venue" => $row["Venue"],
	"city" => $row["City"],
	"category" => $row["Category"],
	"url" => $row["TicketURL"]
	];
}

function doSearchEvents($keyword, $city, $category){
	$keyword = trim($keyword);
	$city = trim($city);
	$category = trim($category);

	$connection = getConnection();

	try {
		//this will only show upcoming events or the events with no date yet
		$sql = "SELECT EventID, EventName, EventDate, Venue, City, Category, TicketURL
				FROM Events
				WHERE (EventDate IS NULL OR EventDate >= CURDATE())";
		$types = "";
		$params = [];

		//escape % and _ so user input is searched literally
		if ($keyword !== ""){
			$like = "%" . addcslashes($keyword, "%_\\") . "%";
			$sql .= " AND (EventName LIKE ? OR Venue LIKE ?)";
			$types .= "ss";
			$params[] = $like;
			$params[] = $like;
		}
		if ($city !== ""){
			$sql .= " AND City LIKE ?";
			$types .= "s";
			$params[] = "%" . addcslashes($city, "%_\\") . "%";
		}
		if ($category !== ""){
			$sql .= " AND Category = ?";
			$types .= "s";
			$params[] = $category;
		}

		$sql .= " ORDER BY EventDate IS NULL, EventDate ASC LIMIT 50";

		$stmt = $connection->prepare($sql);
		if ($types !== ""){
			$stmt->bind_param($types, ...$params);
		}
		$stmt->execute();
		$result = $stmt->get_result();

		$events = [];
		while ($row = $result->fetch_assoc()){
			$events[] = formatEvent($row);
		}
		$stmt->close();

		return [
			"success" => true,
			"events"  => $events
		];
	} catch (Throwable $e){
		error_log("Event search error: " . $e->getMessage());
		return [
			"success" => false,
			"message" => "Failed to search events"
		];
	} finally {
		$connection->close();
	}
}

function doGetEvent($eventId){
	//same check as the web handler, done again here in case someone bypasses it
	if (!preg_match('/^[A-Za-z0-9_-]{1,64}$/', $eventId)){
		return ["success" => false, "message" => "Invalid event"];
	}

	$connection = getConnection();

	try {
		$stmt = $connection->prepare(
			"SELECT EventID, EventName, EventDate, Venue, City, Category, TicketURL
			FROM Events WHERE EventID = ? LIMIT 1"
		);
		$stmt->bind_param("s", $eventId);
		$stmt->execute();
		$row = $stmt->get_result()->fetch_assoc();
		$stmt->close();

		if (!$row){
			return ["success" => false, "message" => "Event not found"];
		}

		return [
			"success" => true,
			"event"   => formatEvent($row)
		];
	} catch (Throwable $e){
		error_log("Get event error: " . $e->getMessage());
		return [
			"success" => false,
			"message" => "Failed to load event"
];
	} finally {
		$connection->close();
	}
}

function doGetRec($sessionToken){
	$validation = doValidate($sessionToken);

	if (!is_array($validation) || ($validation["success"] ?? false) !== true){
		return [
			"success" => false,
			"message" => "Invalid session"
		];
	}
	$userID = (int) $validation["user_id"];
	$connection = getConnection();

	try{
		$stmt = $connection->prepare(
			"SELECT e.Category, e.City
			FROM UserPicks p
			JOIN Events e ON p.EventID = e.EventID
			WHERE p.UserID = ?"
		);

		$stmt->bind_param("i", $userID);
		$stmt->execute();

		$result = $stmt->get_result();

		$categoryCounts = [];
		$cityCounts = [];
		$pickedCount = 0;

		while ($pick = $result->fetch_assoc()){
			$pickedCount++;

			$category = strtolower(trim($pick["Category"] ?? ""));
			$city = strtolower(trim($pick["City"] ?? ""));

			if ($category !== ""){
				$categoryCounts[$category] = ($categoryCounts[$category] ?? 0) + 1;
			}

			if ($city !== ""){
				$cityCounts[$city] = ($cityCounts[$city] ?? 0) + 1;
			}
		}
		$stmt->close();

		$stmt = $connection->prepare(
			"SELECT e.EventID, e.EventDate, e.EventName, e.Venue, e.City, e.Category, e.TicketURL
			FROM Events e
			WHERE e.EventDate >= CURDATE()
				AND NOT EXISTS (
					SELECT 1
					FROM UserPicks p
					WHERE p.UserID = ?
					AND p.EventID = e.EventID
				)
			ORDER BY e.EventDate ASC
			LIMIT 1000"
		);

		$stmt->bind_param("i", $userID);
		$stmt->execute();

		$result = $stmt->get_result();

		$recommendations = [];

		while ($event = $result->fetch_assoc()){
			$category = strtolower(trim($event["Category"] ?? ""));
			$city = strtolower(trim($event["City"] ?? ""));

			$categoryMatches = $category !== ""
				? ($categoryCounts[$category] ?? 0)
				: 0;

			$cityMatches = $city !== ""
				? ($cityCounts[$city] ?? 0)
				: 0;

			//rule based scoring formula
			$score = (40 * $categoryMatches) + (20 * $cityMatches);

			//If theres a new user, show upcoming events, otherwise include matching events
			if ($pickedCount === 0 || $score > 0){
				$event["score"] = $score;
				$recommendations[] = $event;
			}
		}

		$stmt->close();

		//sort rec by score
		usort($recommendations, function ($x, $y){
			if ($x["score"] !== $y["score"]){
				return $y["score"] <=> $x["score"];
			}

			return strcmp($x["EventDate"] ?? "", $y["EventDate"] ?? "");
		});

		//return top 10 events
		return [
			"success" => true,
			"message" => "Recommendations retrieved",
			"recommendations" => array_slice($recommendations, 0, 10)
		];

	} catch (Throwable $e){
		error_log("Recommendation error: " . $e->getMessage());

		return [
			"success" => false,
			"message" => "Unable to retrieve recommendations"
		];
	} finally {
		$connection->close();
	}
}

function doSavePick($sessionToken, $eventID){
	$validation = doValidate($sessionToken);

	if (!is_array($validation) || ($validation["success"] ?? false) !== true){
		//i do my message sending returns like this bc its easier to read and change just in case
		return [
			"success" => false,
			"message" => "Invalid or expired session"
		];
	}

	$userID = (int) $validation["user_id"];
	$eventID = trim ((string) $eventID);

	if ($eventID === ""){
		return [
			"success" => false,
			"message" => "Event ID is required"
		];
	}

	$connection = getConnection();

	try{
		$stmt = $connection->prepare(
			"SELECT EventID
			FROM Events
			WHERE EventID = ?
			LIMIT 1"
		);

		$stmt->bind_param("s", $eventID);
		$stmt->execute();
		$stmt->store_result();

		$eventExists = $stmt->num_rows > 0;

		$stmt->close();

		if (!$eventExists){
			return [
				"success" => false,
				"message" => "Event does not exist"
			];
		}

		//Saving the event to the user's previous picks, the primary key should prevent duplicate slections
		$stmt = $connection->prepare(
			"INSERT IGNORE INTO UserPicks
				(UserID, EventID)
			VALUES (?, ?)"
		); //god i hate sql

		$stmt->bind_param("is", $userID, $eventID);

		$stmt->execute();

		$added = $stmt->affected_rows > 0;

		$stmt->close();

		return [
			"success" => true,
			"message" => $added
				? "Event saved successfully"
				: "Event already saved"
		];
	} catch (Throwable $e){
		error_log("Save pick error: " . $e->getMessage());

		return [
			"success" => false,
			"message" => "Unable to save event"
		];
	} finally{
		$connection->close();
	}
}



function doCreateHangout($sessionToken, $eventID, $inviteeUsernames) 
{
	//get the user session token
	 $result = getUseridFromSession($sessionToken);
	// check if the session is still valid by checking if user id exists
	if ( $result["user_id"] === null) {
		return [
			"success" => false,
			"message" => "Invalid or expired session",
		];

	}

	$uid = $result["user_id"];

// check if there are other memebers inviteed and if not tell there are no guests
// you dont wanna hangout alone :"(
	if(!is_array($inviteeUsernames) || empty ($inviteeUsernames)) {
		return [
			"success" => false,
			"message" => "No invitees provided"
		];
	}
	$connection = getConnection();
	// check if the event even exists in the database
	$stmt = $connection->prepare(
		"SELECT EventID
		FROM Events
		WHERE EventID = ? 
		LIMIT 1"
	);
	$stmt->bind_param("s", $eventID);
	//execute the query
	$stmt->execute();
	/// get the result of the query
	$stmt_result = $stmt->get_result();
	// close the query
	$stmt->close();
// checking if the database had no reults for finding the event

	if (!$row = $stmt_result->fetch_assoc()) {
		return [
		// return event not found if it does not exist
		"success" => false,
		"message" => "Event not found" 
		]; 
	}
	
	//santize the incoming invitees usernames and make sure there is no whitespace
	$inviteeUsernames = array_map('trim', $inviteeUsernames);
	// remove any balnk values
	$inviteeUsernames = array_filter($inviteeUsernames);
	//checking there are no duplicated ids
	$inviteeUsernames = array_unique($inviteeUsernames);

	//array to save the user ids in
	$inviteeIDs =[];
	// check if the username even exist 
	$stmt = $connection->prepare(
		"SELECT uid 
		FROM users
		WHERE username = ?"
	);
	
	$stmt->bind_param("s", $username);
	// add every user invited 
	foreach ($inviteeUsernames as $username){
		$stmt->execute();
		$stmt_result = $stmt->get_result();
		$row = $stmt_result->fetch_assoc();
	// checking if the database had no reults for finding the user
		if (!$row) {
			$stmt->close();
			return [
				// return user not found if it does not exist
				"success" => false,
				"message" => "User not found" 
				]; 
			}

		$inviteeIDs[] = $row ['uid'];

	}
	$stmt->close();
	// removing the creater since thay have been already added to the group
	$inviteeIDs = array_diff($inviteeIDs, [$uid]);
	//checking for duplicate ids
	$inviteeIDs = array_unique($inviteeIDs);

	//dont allow the creater to create hangout for theirselves only
	if (empty($inviteeIDs)) {
		return [
			"success" => false,
			"message" => "Please add at least one more person to create a hangout"
		];
	}
	
	
	try {
		$connection->begin_transaction(); //starting a transaction so the entire batch succeeds or fails
		//inserting vales in hangouts table
		$stmt = $connection->prepare(
			"INSERT into Hangouts (EventID, CreatedID)
			VALUES (?, ?)"
		);
		$stmt->bind_param("si", $eventID, $uid);
		$stmt->execute();
		//returns the value for an auto increment column which in this case is hangoutid
		$hangoutID = $connection->insert_id;
		$stmt->close();

		//inserting values of the creater of hangout in hangoutmembers
		$stmt = $connection->prepare(
			"INSERT into HangoutMembers (HangoutID, MemberID, Response)
			VALUES (?, ?, ?)"
		);
		// creater of the group obviously accepts the invite and is added 
		$response = "accepted";
		$memberID = $uid;
		$stmt->bind_param("iis", $hangoutID, $memberID, $response);
		$stmt->execute();
		$stmt->close();

		//inserting values for each person who is invited
		$stmt = $connection->prepare(
			"INSERT into HangoutMembers (HangoutID, MemberID)
			VALUES (?, ?)");

			$stmt->bind_param("ii", $hangoutID, $memberID);
			//loop to enter all the invitees
			foreach ($inviteeIDs as $guestID) {
				$memberID = $guestID;
				$stmt->execute();
			}
		$stmt->close();
		$connection->commit();

		// hangout has been created
		return [
			"success" => true,
			"message" => "Hangout has been created",
			"hangout_id" => $hangoutID
		];
	}
	catch (Throwable $e) {
		$connection->rollback(); //undo the entire batch if something fails

		error_log("Hangout create error: " . $e->getMessage());

		return [
			"success" => false,
			"message" => "Failed to create a hangout"
		];

	}
	finally {
		$connection->close();
	}
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
			case "sync_events":
				return doSyncEvents($request["events"] ?? []);
			case "search_events":
				return doSearchEvents(
					$request["keyword"]??"",
                                	$request["city"]??"",
                                	$request["category"]??""
				);
			case "get_event":
				return doGetEvent($request["event_id"] ?? "");


			case "get_recommendations":
				return doGetRec($request["session_token"] ?? "");
			case "save_pick":
				return doSavePick($request["session_token"] ?? "", $request["event_id"] ?? "");
			case "create_hangout":
				return doCreateHangout($request["session_token"] ?? "", $request["event_id"] ?? "",
				$request["invitee_usernames"] ?? []);
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
