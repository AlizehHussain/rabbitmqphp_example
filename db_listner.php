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

	$stmt->bind_param("ss", $username, $email);
	$stmt->execute();
	$stmt->store_result();

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
		$connection->begin_transaction(); //starting a transaction so the entire batch succeeds or fails, if it fails i can undo it

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
			if(!is_array($event) || empty($event["event_id"]) || empty($event["name"])){
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
			WHERE p.USERID = ?"
		);

		$stmt->bind_param("i", $userID);
		$stmt->execute();

		$result = $stmt->get_result();

		//these arrays track how frequently different categories and cities appear in the user's saved events
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
			"SELECT e.EventID, e.EventName, e.EventDate,  e.Venue, e.City, e.Category, e.TicketURL
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
		//NOT EXISTS to make sure it doesn't recommend an event a user already picked
		$stmt->bind_param("i", $userID);
		$stmt->execute();

		$result = $stmt->get_results();

		$recommendations = [];

		while ($event = $result->fetch_assoc()){
			//fetch one event at a time as an associative array
			$category = strtolower(trim($event["Category"] ?? ""));
			$city = strtolower(trim($event["City"] ?? ""));

			$categoryMatches = $category !== ""
				? ($categoryCounts[$category] ?? 0)
				: 0;

			$cityMatches = $city !== ""
				? ($cityCounts[$city] ?? 0)
				: 0;

			//rule based scoring formula
			//40 points foe each matching saved category
			//20 points for each matching saved city
			//EXAMPLE:
			//Music event in New York Cat: 2 City: 2 Score: 120
			//Music event in Brooklyn Cat: 2 City: 1 Score: 100
			//Sports event in New York: Cat: 1 City: 2 Score: 80
			$score = (40 * $categoryMatches) + (20 * $cityMatches);

			//If theres a new user, show upcoming events, otherwise include matching events
			if ($pickedCount === 0 || $score > 0){
				$event["score"] = $score;
				$recommendations[] = $event;
			}
		}

		$stmt->close();

		//sort rec by score
		//higher scoring events appear first
		//if two events have the same score, the one with the earlier event date appears first
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

// Puts an email job on emailQueue, where James's emailListener.php picks it up
// and sends it through the team Gmail.
// publish() drops the message on the queue and does NOT wait for an answer,
// so saving an event never gets stuck waiting on the email.
function sendEmailJob($template, $info){
	try {
		// [emailServer] in testRabbitMQ.ini points at emailExchange / emailQueue
		$client = new rabbitMQClient("testRabbitMQ.ini", "emailServer");

		// This is the exact format emailListener.php expects
		$client->publish([
			"type"     => "send_email",
			"template" => $template,   // "pick_confirmation" or "event_reminder"
			"to"       => $info["email"],
			"event"    => [
				"name"  => $info["EventName"],
				"date"  => $info["EventDate"],
				"venue" => $info["Venue"],
				"city"  => $info["City"],
				"url"   => $info["TicketURL"]
			]
		]);
	} catch (Throwable $e){
		// If the email fails, the save still counts. We just log it.
		error_log("Email job error: " . $e->getMessage());
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
		$stmt = $connection->prepare( //makes sure users cant save nonexistent events
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
		//INSERT IGNORE allows sql to skip certain insert errors too
		$stmt = $connection->prepare(
			"INSERT IGNORE INTO UserPicks
				(UserID, EventID)
			VALUES (?, ?)"
		); //god i hate sql

		$stmt->bind_param("is", $userID, $eventID);

		$stmt->execute();

		$added = $stmt->affected_rows > 0; //if sql inserted a new row, the value is true

		$stmt->close();

		// Only email if this is a NEW save (not "Event already saved")
		if ($added){
			// Get the user's email and the event details for the email
			$stmt = $connection->prepare(
				"SELECT u.email, e.EventName, e.EventDate, e.Venue, e.City, e.TicketURL
				FROM users u, Events e
				WHERE u.uid = ? AND e.EventID = ?"
			);
			$stmt->bind_param("is", $userID, $eventID);
			$stmt->execute();
			$info = $stmt->get_result()->fetch_assoc();
			$stmt->close();

			if ($info){
				sendEmailJob("pick_confirmation", $info);
			}
		}

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
	 $result = doValidate($sessionToken);
	// check if the session is still valid by checking if user id exists
	if (!is_array($result) || ($result["success"] ?? false) !== true){
		//i do my message sending returns like this bc its easier to read and change just in case
		return [
			"success" => false,
			"message" => "Invalid or expired session"
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
	$row = $stmt_result->fetch_assoc();
	// close the query
	$stmt->close();
// checking if the database had no reults for finding the event

	if (!$row) {
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
	$username = "";
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

function doGetHangouts($sessionToken){
    // get the user session token
    $result = doValidate($sessionToken);

    // check if the session is still valid
    if (!is_array($result) || ($result["success"] ?? false) !== true){
        return [
            "success" => false,
            "message" => "Invalid or expired session"
        ];
    }

    // get the logged-in user's id
    $userID = (int) $result["user_id"];

    // get connection to the database
    $connection = getConnection();

    try{
        // get hangouts for the logged-in user
        $stmt = $connection->prepare(
            "SELECT
                Hangouts.HangoutID, Events.EventName,
                Events.EventDate,Events.Venue,
                users.username AS CreatorName,
                HangoutMembers.Response
            FROM HangoutMembers
            JOIN Hangouts
                ON HangoutMembers.HangoutID = Hangouts.HangoutID
            JOIN Events
                ON Hangouts.EventID = Events.EventID
            JOIN users
                ON Hangouts.CreatedID = users.uid
            WHERE HangoutMembers.MemberID = ?
            ORDER BY Events.EventDate, Hangouts.HangoutID"
        );

        // bind the logged-in user's id
        $stmt->bind_param("i", $userID);

        // execute the query
        $stmt->execute();

        // get the query results
        $result = $stmt->get_result();

        // array to store the hangouts
        $hangouts = [];

        // fetch each hangout and add it to the array
        while ($row = $result->fetch_assoc()){
            $hangouts[] = $row;

        }

        // close the query
        $stmt->close();

        // return the hangouts
        return [
            "success" => true,
            "message" => "Hangouts retrieved successfully",
            "hangouts" => $hangouts
        ];

    } catch (Throwable $e){
        // log the error
        error_log("Get hangouts error: " . $e->getMessage());

        return [
            "success" => false,
            "message" => "Unable to retrieve hangouts"
        ];

    } finally {
        // close the database connection
        $connection->close();
    }
}
// suggestion eventssss
function doSuggestEvent($sessionToken, $hangoutID, $eventID){

    // check if the user's session is valid
    $result = doValidate($sessionToken);

    // reject the request if the session is invalid
    if (!is_array($result) || ($result["success"] ?? false) !== true){
        return [
            "success" => false,
            "message" => "Invalid or expired session"
        ];
    }

    // get the logged-in user's id
    $userID = (int) $result["user_id"];

    // get connection to the database
    $connection = getConnection();

    try{
        // check if the user is a member of this hangout
        $stmt = $connection->prepare(
            "SELECT HangoutID
            FROM HangoutMembers
            WHERE HangoutID = ? AND MemberID = ?"
        );

        // bind the hangout id and user id
        $stmt->bind_param("ii", $hangoutID, $userID);

        // execute the query
        $stmt->execute();

        // get the query result
        $check = $stmt->get_result();

        // check if the user belongs to this hangout
        $row = $check->fetch_assoc();

        // close the query
        $stmt->close();

        // reject the request if the user is not a member
        if (!$row){
            return [
                "success" => false,
                "message" => "You're not part of this hangout"
            ];
        }

        // check if the event exists
        $stmt = $connection->prepare(
            "SELECT EventID
            FROM Events
            WHERE EventID = ?"
        );

        // bind the event id
        $stmt->bind_param("s", $eventID);

        // execute the query
        $stmt->execute();

        // get the query result
        $check = $stmt->get_result();

        // check if the event exists
        $row = $check->fetch_assoc();

        // close the query
        $stmt->close();

        // reject the request if the event does not exist
        if (!$row){
            return [
                "success" => false,
                "message" => "Event does not exist"
            ];
        }

        // insert the event suggestion
        $stmt = $connection->prepare(
            "INSERT INTO HangoutSuggestions
            (HangoutID, EventID, SuggestedBy)
            VALUES (?, ?, ?)"
        );

        // bind the hangout id, event id and user id
        $stmt->bind_param("isi", $hangoutID, $eventID, $userID);

        // execute the insert
        $stmt->execute();

        // close the query
        $stmt->close();

        // return a success message
        return [
            "success" => true,
            "message" => "Event suggested successfully"
        ];

    } catch (Throwable $e){

        // log the real error
        error_log("Suggest event error: " . $e->getMessage());

        // return a generic error message
        return [
            "success" => false,
            "message" => "Unable to suggest event"
        ];

    } finally {
        // close the database connection
        $connection->close();
    }
}

function doVote($sessionToken, $suggestionID){

    // check if the user's session is valid
    $result = doValidate($sessionToken);

    // reject the request if the session is invalid
    if (!is_array($result) || ($result["success"] ?? false) !== true){
        return [
            "success" => false,
            "message" => "Invalid or expired session"
        ];
    }

    // get the logged-in user's id
    $userID = (int) $result["user_id"];

    // convert the suggestion id to an integer
    $suggestionID = (int) $suggestionID;

    // get connection to the database
    $connection = getConnection();

    try{
        // find the hangout id for this suggestion
        $stmt = $connection->prepare(
            "SELECT HangoutID
            FROM HangoutSuggestions
            WHERE HangoutSuggestionID = ?"
        );

        // bind the suggestion id
        $stmt->bind_param("i", $suggestionID);

        // execute the query
        $stmt->execute();

        // get the query result
        $check = $stmt->get_result();

        // check if the suggestion exists
        $row = $check->fetch_assoc();

        // close the query
        $stmt->close();

        // reject the request if the suggestion does not exist
        if (!$row){
            return [
                "success" => false,
                "message" => "Suggestion not found"
            ];
        }

        // get the hangout id
        $hangoutID = (int) $row["HangoutID"];

        // check if the user is a member of this hangout
        $stmt = $connection->prepare(
            "SELECT HangoutID
            FROM HangoutMembers
            WHERE HangoutID = ? AND MemberID = ?"
        );

        // bind the hangout id and user id
        $stmt->bind_param("ii", $hangoutID, $userID);

        // execute the query
        $stmt->execute();

        // get the query result
        $check = $stmt->get_result();

        // check if the user belongs to this hangout
        $row = $check->fetch_assoc();

        // close the query
        $stmt->close();

        // reject the request if the user is not a member
        if (!$row){
            return [
                "success" => false,
                "message" => "You're not part of this hangout"
            ];
        }

        // insert the vote
        $stmt = $connection->prepare(
            "INSERT INTO HangoutVotes
            (HangoutSuggestionID, UserID)
            VALUES (?, ?)"
        );

        // bind the suggestion id and user id
        $stmt->bind_param("ii", $suggestionID, $userID);

        // execute the insert
        $stmt->execute();

        // close the query
        $stmt->close();

        // return a success message
        return [
            "success" => true,
            "message" => "Vote submitted successfully"
        ];

    } catch (Throwable $e){

        // log the real error
        error_log("Vote error: " . $e->getMessage());

        // return a generic error message
        return [
            "success" => false,
            "message" => "Unable to submit vote"

        ];

    } finally {
        // close the database connection
        $connection->close();
    }
}

function doGetSuggestions($sessionToken, $hangoutID){

    // check if the user's session is valid
    $result = doValidate($sessionToken);

    // reject the request if the session is invalid
    if (!is_array($result) || ($result["success"] ?? false) !== true){
        return [
            "success" => false,
            "message" => "Invalid or expired session"
        ];
    }

    // get the logged-in user's id
    $userID = (int) $result["user_id"];

    // convert the hangout id to an integer
    $hangoutID = (int) $hangoutID;

    // get connection to the database
    $connection = getConnection();

    try{
        // check if the user is a member of this hangout
        $stmt = $connection->prepare(
            "SELECT HangoutID
            FROM HangoutMembers
            WHERE HangoutID = ? AND MemberID = ?"
        );

        // bind the hangout id and user id
        $stmt->bind_param("ii", $hangoutID, $userID);

        // execute the query
        $stmt->execute();

        // get the query result
        $check = $stmt->get_result();

        // check if the user belongs to this hangout
        $row = $check->fetch_assoc();

        // close the query
        $stmt->close();

        // reject the request if the user is not a member
        if (!$row){
            return [
                "success" => false,
                "message" => "You're not part of this hangout"
            ];
        }

        // get all suggestions for this hangout
        $stmt = $connection->prepare(
            "SELECT
                HangoutSuggestions.HangoutSuggestionID,
                Events.EventName,
                Events.EventDate,
                Events.Venue,
                COUNT(HangoutVotes.UserID) AS VoteCount,
                SUM(CASE
                    WHEN HangoutVotes.UserID = ? THEN 1
                    ELSE 0
                END) AS UserVoted
            FROM HangoutSuggestions
            JOIN Events
                ON HangoutSuggestions.EventID = Events.EventID
            LEFT JOIN HangoutVotes
                ON HangoutSuggestions.HangoutSuggestionID = HangoutVotes.HangoutSuggestionID
            WHERE HangoutSuggestions.HangoutID = ?
            GROUP BY
                HangoutSuggestions.HangoutSuggestionID,
                Events.EventName,
                Events.EventDate,
                Events.Venue
            ORDER BY HangoutSuggestions.HangoutSuggestionID"
        );

        // bind the user id and hangout id
        $stmt->bind_param("ii", $userID, $hangoutID);

        // execute the query
        $stmt->execute();

        // get the query result
        $check = $stmt->get_result();

        // create an array for the suggestions
        $suggestions = [];

        // loop through the results and add each suggestion to the array
        while ($row = $check->fetch_assoc()){
            $suggestions[] = $row;
        }

        // close the query
        $stmt->close();

        // return the suggestions
        return [
            "success" => true,
            "message" => "Suggestions retrieved successfully",
            "suggestions" => $suggestions
        ];

    } catch (Throwable $e){

        // log the real error
        error_log("Get suggestions error: " . $e->getMessage());

        // return a generic error message
        return [
            "success" => false,
            "message" => "Unable to retrieve suggestions"

        ];

    } finally {
        // close the database connection
        $connection->close();
    }
}


function doGetEvents(){
	$connection = getConnection();

	try{
		$stmt = $connection->prepare(
			"SELECT EventID, EventName, EventDate, Venue, City, Category, TicketURL
			FROM Events
			WHERE EventDate >= CURDATE()
			ORDER BY EventDate ASC
			LIMIT 50"
		);

		$stmt->execute();
		$result = $stmt->get_result();

		$events = [];

		while ($event = $result->fetch_assoc()){
			$events[] = $event;
		}

		$stmt->close();

		return [
			"success" => true,
			"events" => $events
		];
	} catch (Throwable $e){
		error_log("Get events error: " . $e->getMessage());

		return [
			"success" => false,
			"message" => "Unable to retrieve events"
		];
	} finally {
		$connection->close();
	}
}

function doSendFriendRequest($sessionToken, $friendUsername){
	$validation = doValidate($sessionToken);

	if (!is_array($validation) || ($validation["success"] ?? false) !== true){
		return [
			"success" => false,
			"message" => "Invalid session"
		];
	}

	$senderID = (int) $validation["user_id"];
	$friendUsername = trim((string) $friendUsername);

	if ($friendUsername === ""){
		return [
			"success" => false,
			"message" => "Friend's username is required"
		];
	}

	$connection = getConnection();

	try{
		$stmt = $connection->prepare(
			"SELECT uid
			FROM users
			WHERE username = ?
			LIMIT 1"
		);

		$stmt->bind_param("s", $friendUsername);
		$stmt->execute();

		$result = $stmt->get_result();
		$friend = $result->fetch_assoc();

		$stmt->close();

		if (!$friend){
			return [
				"success" => false,
				"message" => "User not found"
			];
		}

		$receiverID = (int) $friend["uid"];

		if ($senderID === $receiverID){
			return [
				"success" => false,
				"message" => "You can't friend yourself"
			];
		}

		//checking for existing friendships and requests
		$stmt = $connection->prepare(
			"SELECT Status
			FROM Friends
			WHERE
				(SenderID = ? AND ReceiverID = ?)
				OR
				(SenderID = ? AND ReceiverID = ?)
			LIMIT 1"
		);

		$stmt->bind_param(
			"iiii",
			$senderID,
			$receiverID,
			$receiverID,
			$senderID
		);

		$stmt->execute();

		$result = $stmt->get_result();
		$existing = $result->fetch_assoc();

		$stmt->close();

		//this next portion will prevent duplicate friend requests

		if ($existing){
			if ($existing["Status"] === "accepted"){
				return [
					"success" => false,
					"message" => "You are already friends"
				];
			}

			if ($existing["Status"] === "pending"){
				return [
					"success" => false,
					"message" => "A friend request is already pending"
				];
			}

			//ik same thing below but its for a declined request
			return [
				"success" => false,
				"message" => "Friend request declined"
			];
		}

		//insert the new friend request
		$stmt = $connection->prepare(
			"INSERT INTO Friends
				(SenderID, ReceiverID, Status)
			VALUES (?, ?, 'pending')"
		);

		$stmt->bind_param(
			"ii",
			$senderID,
			$receiverID
		);

		$stmt->execute();
		$stmt->close();

		return [
			"success" => true,
			"message" => "Friend request sent successfully"
		];
	} catch (Throwable $e){
		error_log("Friend request error: " . $e->getMessage());

		return [
			"success" => false,
			"message" => "Unable to send friend request"
		];
	} finally {
		$connection->close();
	}
}

function doGetFriendRequests($sessionToken){
	$validation = doValidate($sessionToken);

	if (!is_array($validation) || ($validation["success"] ?? false) !== true){
		return [
			"success" => false,
			"message" => "Invalid session"
		];
	}
	//get logged in user id
	$userID = (int) $validation["user_id"];

	$connection = getConnection();

	try{
		$stmt = $connection->prepare(
			"SELECT
				f.FriendID,
				f.SenderID,
				u.username,
				f.CreatedAt
			FROM Friends f
			JOIN users u ON f.SenderID = u.uid
			WHERE f.ReceiverID = ?
				AND f.Status = 'pending'
			ORDER BY f.CreatedAt DESC"
		);

		$stmt->bind_param("i", $userID);
		$stmt->execute();

		$result = $stmt->get_result();

		$requests = [];

		while ($row = $result->fetch_assoc()){
			$requests[] = $row;
		}

		$stmt->close();

		return [
			"success" => true,
			"message" => "Friend request retrieved",
			"requests" => $requests
		];
	} catch (Throwable $e){
		error_log("Get friend requests error: " . $e->getMessage());

		return [
			"success" => false,
			"message" => "Unable to retrieve friend requests"
		];
	} finally {
		$connection->close();
	}
}

function doRespondFriendRequest($sessionToken, $friendID, $action){
	$validation = doValidate($sessionToken);

	if (!is_array($validation) || ($validation["success"] ?? false) !== true){
		return [
			"success" => false,
			"message" => "Invalid session"
		];
	}

	$userID = (int) $validation["user_id"];

	$friendID = filter_var($friendID, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);

	$action = strtolower(trim((string) $action));

	if ($friendID === false){
		return [
			"success" => false,
			"message" => "Invalid friend request ID"
		];
	}

	if ($action !== "accept" && $action !== "decline"){
		return [
			"success" => false,
			"message" => "Action must be accept or decline"
		];
	}

	$connection = getConnection();

	try{
		//determining the friendship status
		$newStatus = $action === "accept"
			? "accepted"
			: "declined";

		$stmt = $connection->prepare(
			"UPDATE Friends
			SET Status = ?
			WHERE FriendID = ?
			AND ReceiverID = ?
			AND Status = 'pending'"
		);

		$stmt->bind_param(
			"sii",
			$newStatus,
			$friendID,
			$userID
		);

		$stmt->execute();

		$updated = $stmt->affected_rows;

		$stmt->close();

		//check whether a request was updated
		if ($updated === 0){
			return [
				"success" => false,
				"message" => "Pending friend request not found"
			];
		}

		return [
			"success" => true,
			"message" => $action === "accept"
				? "Friend request accepted"
				: "Friend request declined"
		];
	} catch (Throwable $e){
		error_log("Respond friend request error: " . $e->getMessage());

		return [
			"success" => false,
			"message" => "Unable to respond to friend request"
		];
	} finally {
		$connection->close();
	}
}

function doGetFriends($sessionToken){
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
			"SELECT
				u.uid,
				u.username,
				f.FriendID
			FROM Friends f
			JOIN users u
				ON u.uid = CASE
					WHEN f.SenderID = ? THEN f.ReceiverID
					ELSE f.SenderID
				END
			WHERE
				(f.SenderID = ? OR f.ReceiverID = ?)
				AND f.Status = 'accepted'
			ORDER BY u.username ASC"
		);

		$stmt->bind_param(
			"iii",
			$userID,
			$userID,
			$userID
		);

		$stmt->execute();

		$result = $stmt->get_result();

		$friends = [];

		while ($row = $result->fetch_assoc()){
			$friends[] = $row;
		}

		$stmt->close();

		return [
			"success" => true,
			"message" => "Friends retrieve succcessfully",
			"friends" => $friends
		];
	} catch (Throwable $e){
		error_log("Get friends error: " . $e->getMessage());

		return [
			"success" => false,
			"message" => "Unable to retrieve friends"
		];
	} finally {
		$connection->close();
	}
}

function doCreateGroup($sessionToken, $groupName)
{
    // check if the session is valid
    $result = doValidate($sessionToken);

    if (!is_array($result) || ($result["success"] ?? false) !== true) {
        return [
            "success" => false,
            "message" => "Invalid session or expired session"
        ];
    }

    // get the user id
    $userID = (int) $result["user_id"];

    // clean the group name
    $groupName = trim($groupName);

    // check if the group name is empty
    if ($groupName === "") {
        return [
            "success" => false,
            "message" => "Group name is required"
        ];
    }

    // check if the group name is longer than 100 characters
    if (strlen($groupName) > 100) {
        return [
            "success" => false,
            "message" => "Group name cannot be longer than 100 characters"
        ];
    }

    // connect to the database
    $connection = getConnection();

    try {
        // start the transaction
        $connection->begin_transaction();

        // insert the new group
        $stmt = $connection->prepare(
            "INSERT INTO FriendGroups (GroupName, CreatedID) VALUES (?, ?)"
        );
        $stmt->bind_param("si", $groupName, $userID);
        $stmt->execute();

        // get the new group id
        $groupID = $connection->insert_id;

        $stmt->close();

        // add the creator as a group member
        $stmt = $connection->prepare(
            "INSERT INTO GroupMembers (GroupID, MemberID) VALUES (?, ?)"
        );
        $stmt->bind_param("ii", $groupID, $userID);
        $stmt->execute();
        $stmt->close();

        // save both inserts
        $connection->commit();

        return [
            "success" => true,
            "message" => "Group created successfully",
            "group_id" => $groupID
        ];
    }
    catch (Throwable $e) {

        // undo the inserts if something goes wrong
        $connection->rollback();

        // log the error
        error_log("Create group error: " . $e->getMessage());

        return [
            "success" => false,
            "message" => "Unable to create group"
        ];
    }
    finally {

        // close the database connection
        $connection->close();
    }
}

function doGetGroups($sessionToken)
{
    // check if the session is valid
    $result = doValidate($sessionToken);

    if (!is_array($result) || ($result["success"] ?? false) !== true) {
        return [
            "success" => false,
            "message" => "Invalid session or expired session"
        ];
    }

    // get the user id
    $userID = (int) $result["user_id"];

    // connect to the database
    $connection = getConnection();

    try {

        // get all groups that the user is a member of
        $stmt = $connection->prepare(
            "SELECT FriendGroups.GroupID, FriendGroups.GroupName
            FROM GroupMembers
            JOIN FriendGroups
                ON GroupMembers.GroupID = FriendGroups.GroupID
            WHERE GroupMembers.MemberID = ?
            ORDER BY FriendGroups.GroupName"
        );

        $stmt->bind_param("i", $userID);
        $stmt->execute();

        // get the group information
        $check = $stmt->get_result();

        // create an empty list for the groups
        $groups = [];

        // go through each group and add it to the list
        while ($row = $check->fetch_assoc()) {
            $groups[] = $row;
        }

        $stmt->close();

        return [
            "success" => true,
            "message" => "Groups retrieved successfully",
            "groups" => $groups
        ];
    }
    catch (Throwable $e) {

        // log the error
        error_log("Get groups error: " . $e->getMessage());

        return [
            "success" => false,
            "message" => "Unable to retrieve groups"

        ];
    }
    finally {

        // close the database connection
        $connection->close();
    }
}

function doCreateInvite($sessionToken, $groupID)
{
    // check if the session is valid
    $result = doValidate($sessionToken);

    if (!is_array($result) || ($result["success"] ?? false) !== true) {
        return [
            "success" => false,
            "message" => "Invalid session"
        ];
    }

    // get the user id and group id
    $userID = (int) $result["user_id"];
    $groupID = (int) $groupID;

    // connect to the database
    $connection = getConnection();

    try {

        // check if the user is part of this group
        $stmt = $connection->prepare(
            "SELECT GroupID
            FROM GroupMembers
            WHERE GroupID = ? AND MemberID = ?"
        );

        $stmt->bind_param("ii", $groupID, $userID);
        $stmt->execute();

        $check = $stmt->get_result();

        // check if the user is a member of the group
        if ($check->num_rows === 0) {
            $stmt->close();

            return [
                "success" => false,
                "message" => "You're not part of this group"
            ];
        }

        $stmt->close();

        // generate the invite token
        $rawToken = keyGenerator($userID);

        // hash the token before storing it
        $inviteToken = hash('sha256', $rawToken);

        // insert the invite into the database
        $stmt = $connection->prepare(
            "INSERT INTO GroupInvites (GroupID, InviteToken, CreatedBy, ExpiresAt)
            VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 7 DAY))"
        );

        $stmt->bind_param("isi", $groupID, $inviteToken, $userID);
        $stmt->execute();
        $stmt->close();

        return [
            "success" => true,
            "message" => "Invite created successfully",
            "invite_token" => $rawToken
        ];
    }
    catch (Throwable $e) {

        // log the error
        error_log("Create invite error: " . $e->getMessage());

        return [
            "success" => false,
            "message" => "Unable to create invite"

        ];
    }
    finally {

        // close the database connection
        $connection->close();
    }
}

function doJoinGroup($sessionToken, $inviteToken)
{
    // check if the session is valid
    $result = doValidate($sessionToken);

    if (!is_array($result) || ($result["success"] ?? false) !== true) {
        return [
            "success" => false,
            "message" => "Invalid session"
        ];
    }

    // get the user id
    $userID = (int) $result["user_id"];

    // check if the invite token is empty
    $inviteToken = trim($inviteToken);

    if ($inviteToken === "") {
        return [
            "success" => false,
            "message" => "Invite token is required"
        ];
    }

    // hash the invite token
    $inviteTokenHash = hash('sha256', $inviteToken);

    // connect to the database
    $connection = getConnection();

    try {

        // check if the invite token is valid and has not expired
        $stmt = $connection->prepare(
            "SELECT GroupID
            FROM GroupInvites
            WHERE InviteToken = ? AND ExpiresAt > NOW()"
        );

        $stmt->bind_param("s", $inviteTokenHash);
        $stmt->execute();

        $check = $stmt->get_result();

        // check if the invite exists
        if ($check->num_rows === 0) {
            $stmt->close();

            return [
                "success" => false,
                "message" => "Invalid or expired invite"
            ];
        }

        // get the group id
        $row = $check->fetch_assoc();
        $groupID = (int) $row["GroupID"];

        $stmt->close();

        // check if the user is already in the group
        $stmt = $connection->prepare(
            "SELECT GroupID
            FROM GroupMembers
            WHERE GroupID = ? AND MemberID = ?"
        );

        $stmt->bind_param("ii", $groupID, $userID);
        $stmt->execute();

        $check = $stmt->get_result();

        // check if the user is already a member
        if ($check->num_rows > 0) {
            $stmt->close();

            return [
                "success" => true,
                "message" => "You're already in this group"
            ];
        }

        $stmt->close();

        // add the user to the group
        $stmt = $connection->prepare(
            "INSERT INTO GroupMembers (GroupID, MemberID)
            VALUES (?, ?)"
        );

        $stmt->bind_param("ii", $groupID, $userID);
        $stmt->execute();
        $stmt->close();

        return [
            "success" => true,
            "message" => "You joined the group successfully"
        ];
    }
    catch (Throwable $e) {

        // log the error
        error_log("Join group error: " . $e->getMessage());

        return [
            "success" => false,
            "message" => "Unable to join group"

        ];
    }
    finally {

        // close the database connection
        $connection->close();
    }
}

function doGetGroupMembers($sessionToken, $groupID)

{
    // check if the session is valid
    $result = doValidate($sessionToken);

    if (!is_array($result) || ($result["success"] ?? false) !== true) {
        return [
            "success" => false,
            "message" => "Invalid session"
        ];
    }

    // get the user id and group id
    $userID = (int) $result["user_id"];
    $groupID = (int) $groupID;

    // connect to the database
    $connection = getConnection();

    try {

        // check if the user is part of this group
        $stmt = $connection->prepare(
            "SELECT GroupID
            FROM GroupMembers
            WHERE GroupID = ? AND MemberID = ?"
        );

        $stmt->bind_param("ii", $groupID, $userID);
        $stmt->execute();

        $check = $stmt->get_result();

        // check if the user is a member of the group
        if ($check->num_rows === 0) {
            $stmt->close();

            return [
                "success" => false,
                "message" => "You're not part of this group"
            ];
        }

        $stmt->close();

        // get the usernames of the group members
        $stmt = $connection->prepare(
            "SELECT users.username
            FROM GroupMembers
            JOIN users
                ON GroupMembers.MemberID = users.uid
            WHERE GroupMembers.GroupID = ?"
        );

        $stmt->bind_param("i", $groupID);
        $stmt->execute();

        // get the member information
        $check = $stmt->get_result();

        // create an empty list for the members
        $members = [];

        // go through each member and add them to the list
        while ($row = $check->fetch_assoc()) {
            $members[] = $row;
        }

        $stmt->close();

        return [
            "success" => true,
            "message" => "Group members retrieved successfully",
            "members" => $members
        ];
    }
    catch (Throwable $e) {

        // log the error
        error_log("Get group members error: " . $e->getMessage());

        return [
            "success" => false,
            "message" => "Unable to retrieve group members"

        ];
    }
    finally {

        // close the database connection
        $connection->close();
    }
}

function doRateEvent($eventID, $sessionToken, $clicked, $rating){		
	$validation = doValidate($sessionToken);
	$connection = getConnection();
	$userID = (int) $validation["user_id"];

	try{
		$stmt = $connection->prepare(
			"SELECT EventID, EventName, EventDate, Venue, City, Category, TicketURL
			FROM Events
			WHERE EventID = ?"
		);
		$stmt->bind_param("s",$eventID);

		$stmt->execute();
		//returns matching array to eventID
		$event = $stmt->get_result()->fetch_assoc();
		$stmt->close();
			

		if ($clicked){
		$stmt = $connection->prepare(
			"INSERT INTO Ratings (EventID, UserID, Rating) Values (?,?,?) ON DUPLICATE KEY UPDATE Rating = VALUES(Rating)");
		$stmt->bind_param("sii", $eventID, $userID, $rating);
		$stmt->execute();
		$stmt->close();
		}
		
		$stmt = $connection->prepare(
			"SELECT COUNT(*) AS total_rows FROM Ratings WHERE EventID = ?");
		$stmt->bind_param("s", $eventID);
		$stmt->execute();
		$nullCheck = $stmt->get_result()->fetch_assoc();
		$stmt->close();
		if ($nullCheck["total_rows"] > 0) { 
		$stmt = $connection->prepare(
			"SELECT AVG(Rating) AS average FROM Ratings WHERE EventID = ?");
		$stmt->bind_param("s", $eventID);
		$stmt->execute();
		$average = $stmt->get_result()->fetch_assoc();
		$stmt->close();
		return [
			"success" => true,
			"event" => $event,
			"average" => $average["average"]
		];
		}
		return [
			"success" => true,
			"event" => $event,
		];
	} catch (Throwable $e){
		error_log("Event ID error: " . $e->getMessage());

		return [
			"success" => false,
			"message" => "Unable to retrieve events"
		];
	} finally {
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
			case "get_recommendations":
				return doGetRec($request["session_token"] ?? "");
			case "save_pick":
				return doSavePick($request["session_token"] ?? "", $request["event_id"] ?? "");
			case "create_hangout":
				return doCreateHangout($request["session_token"] ?? "", $request["event_id"] ?? "",
				$request["invitee_usernames"] ?? []);
			case "get_hangouts":
				return doGetHangouts($request["session_token"] ?? "");
			case "get_events":
				return doGetEvents();
			case "suggest_event":
				return doSuggestEvent ($request["session_token"] ?? "", $request["hangout_id"] ?? "",
				$request["event_id"] ?? "");
			case "vote_event":
				return doVote ($request["session_token"] ?? "", $request["suggestion_id"] ?? "");
			case "get_suggestions":
				return doGetSuggestions ($request["session_token"] ?? "", $request["hangout_id"] ?? "");	
			case "send_friend_request":
				return doSendFriendRequest($request["session_token"] ?? "", $request["friend_username"] ?? "");
			case "get_friend_requests":
				return doGetFriendRequests($request["session_token"] ?? "");
			case "respond_friend_request":
				return doRespondFriendRequest($request["session_token"] ?? "", $request["friend_id"] ?? 0, $request["action"] ?? "");
			case "get_friends":
				return doGetFriends($request["session_token"] ?? "");
			case "create_group":
				return doCreateGroup($request["session_token"] ?? "", $request["group_name"] ?? "");
			case "get_groups":
				return doGetGroups($request["session_token"] ?? "");
			case "create_invite":
				return doCreateInvite($request["session_token"] ?? "", $request["group_id"] ?? "");
			case "join_group":
				return doJoinGroup($request["session_token"] ?? "", $request["invite_token"] ?? "");
			case"get_group_members":
				return doGetGroupMembers($request["session_token"] ?? "", $request["group_id"] ?? "");
			case "rateEvent":
				return doRateEvent($request["event_ID"] ?? "", $request["sessionToken"] ?? "", $request["clicked"] ?? "", $request["rating"] ?? "");
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
