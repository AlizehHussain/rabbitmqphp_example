<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Event Details</title>
<style>
.rating {
  margin-top: 0px;
  border: none;
  display: inline-block;
}

.rating > label {
  color: #90A0A3;
  float: right;
}

.rating > label:before {
  margin: 5px;
  font-size: 2em;
  content: "\2605"; /* ★ */
  display: inline-block;
}

.rating > input {
  display: none;
}

.rating > input:checked ~ label,
.rating:not(:checked) > label:hover,
.rating:not(:checked) > label:hover ~ label {
  color: #F79426;
}

.rating > input:checked + label:hover,
.rating > input:checked ~ label:hover,
.rating > label:hover ~ input:checked ~ label,
.rating > input:checked ~ label:hover ~ label {
  color: #FECE31;
}
</style>
</head>
<body>

<!-- Everything is inside this div and stays hidden until we make sure the user is logged in -->
<div id="content" hidden>
    <p><a href="home.php">Home</a> | <a href="logout.php">Log out</a></p>

 <!-- Shows "Loading..." or an error message if something goes wrong -->
    <p id="status">Loading...</p>

 <!-- The event details, hidden until the data arrives.
         Each empty span gets filled in by JavaScript using its id -->
    <div id="details" hidden>
	<h1 id="name"></h1>
<div class="rating">
  <input type="radio" id="star5" name="rating" value="5" />
  <label class="star" for="star5" title="Awesome" aria-hidden="true"></label>
  <input type="radio" id="star4" name="rating" value="4" />
  <label class="star" for="star4" title="Great" aria-hidden="true"></label>
  <input type="radio" id="star3" name="rating" value="3" />
  <label class="star" for="star3" title="Very good" aria-hidden="true"></label>
  <input type="radio" id="star2" name="rating" value="2" />
  <label class="star" for="star2" title="Good" aria-hidden="true"></label>
  <input type="radio" id="star1" name="rating" value="1" />
  <label class="star" for="star1" title="Bad" aria-hidden="true"></label>
</div>
<span id="globalAvg">Global avg: ...</span>
        <p><strong>Date:</strong> <span id="date"></span></p>
        <p><strong>Venue:</strong> <span id="venue"></span></p>
        <p><strong>City:</strong> <span id="city"></span></p>
        <p><strong>Category:</strong> <span id="category"></span></p>
 <!-- The ticket link is only shown if the event has a valid web address -->
        <p><a id="ticketLink" hidden>Buy tickets on Ticketmaster</a></p>
 <!-- The Save button, with a spot next to it for error messages -->

        <p><button id="saveBtn">Save Event</button><span id="saveMsg"></span></p>
</div>
</div>

<!-- Loads the shared Save button code we made -->
<script src="saveEvent.js"></script>

<script>

// Sends data to one of our PHP files and returns the PHP file's answer
async function sendRequest(url, data) {
    const response = await fetch(url, {
        method: "POST", // send data to the server
        headers: { "Content-Type": "application/json" }, // tell PHP we're sending JSON
        body: JSON.stringify(data) // turn our data into JSON text
    });
    return response.json(); // turn PHP's JSON answer back into an object
}

// Checks that the user is logged in. Returns true if they are, if they're not sends them to the login page.
async function checkLogin() {
    const token = sessionStorage.getItem('session_token');

 // No token means they never logged in
    if (!token) {
        window.location.href = "login.php";
        return false;
    }

    try {
// Asks the server if the token is still valid
        const data = await sendRequest("sessionValidator.php", {
            typeRequest: 'validate_session',
            session_token: token
        });

        if (data.success) {
            return true;
        }

// If the token expired or isn't real it removes it and goes to login
        sessionStorage.removeItem('session_token');
        window.location.href = "login.php";
        return false;
    } catch (e) {
        window.location.href = "login.php";
        return false;
    }
}

// Loads one event's details and shows them on the page
async function loadEvent() {
    const status = document.getElementById('status');

// Read the event ID from the address, e.g. event.php?id=whatevrabsbshaua
const params = new URLSearchParams(window.location.search);
const eventId = params.get('id');
// No ID in the address means there's nothing to show.
// (The server checks the ID's format, so we only check that one exists.)
    if (!eventId) {
        status.textContent = "Event not found.";
        return;
    }

    try {
// Get the list of upcoming events from get_events_handler.php
// (the same list the home page shows). It's a GET request, so we send no data.
        const response = await fetch("get_events_handler.php");
        const data = await response.json();

// If something went wrong on the server
        if (!data.success) {
            status.textContent = data.message || "Could not load event.";
            return;
        }

// find() goes through the list and gives back the event whose ID
// matches the one in the address (or undefined if none match)
        const ev = data.events.find(function (e) {
            return e.EventID === eventId;
        });

        if (!ev) {
            status.textContent = "Event not found.";
            return;
        }

        // Put the event name in the browser tab
        document.title = ev.EventName;

        // Fill in each field. textContent shows plain text only, so event data
        // can't inject code into the page. "||" gives a fallback when a field is empty.
        document.getElementById('name').textContent = ev.EventName;
        document.getElementById('date').textContent = ev.EventDate || "Date TBA";
        document.getElementById('venue').textContent = ev.Venue || "TBA";
        document.getElementById('city').textContent = ev.City || "TBA";
        document.getElementById('category').textContent = ev.Category || "Other";

        // Only show the ticket link if it's a normal https web address
        if (ev.TicketURL && ev.TicketURL.startsWith("https://")) {
            const link = document.getElementById('ticketLink');
            link.href = ev.TicketURL;
            link.hidden = false;
        }

        // Connect the Save button to this event (saveEvent is in saveEvent.js)
	const saveBtn = document.getElementById('saveBtn');
	saveBtn.addEventListener('click', function () {
                   saveEvent(eventId, saveBtn, document.getElementById('saveMsg'));
        });

        // Hide "Loading..." and show the details
        status.hidden = true;
        document.getElementById('details').hidden = false;
    } catch (e) {
        status.textContent = "Could not reach the server.";
    }
}

// Runs once when the page loads: check login, then show the page and load the event
async function startPage() {
    const loggedIn = await checkLogin();
    if (loggedIn) {
        document.getElementById('content').hidden = false;
        loadEvent();
    }
}


startPage();
</script>
</body>
</html>
