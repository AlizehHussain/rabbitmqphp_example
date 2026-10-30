<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Home</title>
</head>
<body>

<!-- Everything on this page is inside this div, so it starts hidden and only
shows once we've checked that the user is logged in -->
<div id="content" hidden>
    <h1>Welcome!</h1>
    <p><a href="create_hangout.html">Create a Hangout</a> | <a href="my_hangouts.html">My Hangouts</a> | <a href="logout.php">Log out</a></p>


    <h2>Upcoming Events</h2>

<!-- The search form. Every box is optional, so leaving them all
empty shows all events (which is what loads when the page opens) -->
 <form id="searchForm">
        <input type="text" id="keyword" placeholder="Artist, event, or venue">
        <input type="text" id="city" placeholder="City">

<!-- These category names match what Ticketmaster uses, which is what Nick's script saves in the database -->
        <select id="category">
            <option value="">All categories</option>
            <option value="Music">Music</option>
            <option value="Sports">Sports</option>
            <option value="Arts &amp; Theatre">Arts &amp; Theatre</option>
            <option value="Family">Family</option>
            <option value="Miscellaneous">Miscellaneous</option>
        </select>
        <button type="submit">Search</button>
    </form>

<!-- Shows messages like "Searching..." or "3 event(s) found" -->
    <p id="status"></p>

<!-- The search results get added into this list by JavaScript -->
 <ul id="results"></ul>
</div>

<!-- This line just loads the shared Save button code -->
<script src="saveEvent.js"></script>

<script>
// Sends data to one of our PHP files and returns the PHP file's answer.
// We use this for every request on this page.
async function sendRequest(url, data) {
    const response = await fetch(url, {
        method: "POST", // sends data to the server
        headers: { "Content-Type": "application/json" }, // tells PHP we're sending JSON
        body: JSON.stringify(data) // turn our data into JSON text
    });
    return response.json(); // turns PHP's JSON answer back into an object
}

// Checks that the user is logged in. Returns true if they are.
// If they aren't, it sends them to the login page.
async function checkLogin() {
// The login page saved the session token in the browser
const token = sessionStorage.getItem('session_token');

// No token means they never logged in
    if (!token) {
        window.location.href = "login.php";
        return false;
    }

    try {
 // Ask the server if the token is still valid by checking the sessions table
        const data = await sendRequest("sessionValidator.php", {
            typeRequest: 'validate_session',
            session_token: token
        });

        if (data.success) {
            return true;    // logged in
        }

// If the token expired or isn't real it removes it and goes to login
        sessionStorage.removeItem('session_token');
        window.location.href = "login.php";
        return false;
    } catch (e) {
// If the server couldn't be reached it sends them to login to be safe
        window.location.href = "login.php";
        return false;
    }
}

// Gets all upcoming events from get_events_handler.php.
// It's a GET request, so we don't send any data. We just ask for the list.
async function getAllEvents() {
    const response = await fetch("get_events_handler.php");
    return response.json(); // turns PHP's JSON answer back into an object
}

// Gets the events, keeps only the ones that match the search boxes,
// and shows them in the list
async function searchEvents() {
    const status = document.getElementById('status');
    const list = document.getElementById('results');

    status.textContent = "Searching...";
    list.innerHTML = ""; // clears out the old results first

    try {
// get_events_handler.php sends "get_events" through RabbitMQ to the
// database listener, which sends back all upcoming events
        const data = await getAllEvents();

// If something went wrong on the server side
        if (!data.success) {
            status.textContent = data.message || "Search failed.";
            return;
        }

// Read what the user typed. toLowerCase() so "newark" still matches "Newark"
        const keyword = document.getElementById('keyword').value.trim().toLowerCase();
        const city = document.getElementById('city').value.trim().toLowerCase();
        const category = document.getElementById('category').value;

// filter() keeps only the events where this function returns true.
// An empty box is skipped, so it doesn't filter anything out.
        const events = data.events.filter(function (ev) {
            const name = (ev.EventName || "").toLowerCase();
            const venue = (ev.Venue || "").toLowerCase();
            const evCity = (ev.City || "").toLowerCase();

            // keyword can be in the event name OR the venue
            if (keyword !== "" && !name.includes(keyword) && !venue.includes(keyword)) {
                return false;
            }
            // city just has to contain what they typed
            if (city !== "" && !evCity.includes(city)) {
                return false;
            }
            // category has to match exactly, since it comes from the dropdown
            if (category !== "" && ev.Category !== category) {
                return false;
            }
            return true;
        });

// If nothing matched
        if (events.length === 0) {
            status.textContent = "No events found.";
            return;
        }

        status.textContent = events.length + " event(s) found";

// Build one list item for each event
        for (const ev of events) {
            const li = document.createElement('li');

// Event name as a link to its detail page.
// encodeURIComponent makes the ID safe to put in a web address.
            const link = document.createElement('a');
            link.href = "event.php?id=" + encodeURIComponent(ev.EventID);
// textContent shows the name as plain text, so if a name ever
// contained HTML or a script, it would display instead of running
            link.textContent = ev.EventName;
            li.appendChild(link);

// Date, venue, and city after the name ("Date TBA" if there's no date)
            let details = " — " + (ev.EventDate || "Date TBA") + ", " + (ev.Venue || "");
            if (ev.City) {
                details = details + ", " + ev.City;
            }
            li.appendChild(document.createTextNode(details + " "));

// Save button for this event (feeds Nick's recommendation system)
            const saveBtn = document.createElement('button');
            saveBtn.textContent = "Save Event";
            const saveMsg = document.createElement('span'); // shows errors next to the button
            saveBtn.addEventListener('click', function () {
                saveEvent(ev.EventID, saveBtn, saveMsg); // from saveEvent.js
            });
            li.appendChild(saveBtn);
            li.appendChild(saveMsg);

            list.appendChild(li);
        }
    } catch (e) {
        status.textContent = "Could not reach the server.";
    }
}

// When the Search button is pressed, run our search instead of
// letting the browser reload the page (which is what forms do by default)
document.getElementById('searchForm').addEventListener('submit', function (e) {
    e.preventDefault();
    searchEvents();
});

// Runs once when the page loads: checks login, then shows the page and all events
async function startPage() {
    const loggedIn = await checkLogin();
    if (loggedIn) {
        document.getElementById('content').hidden = false;
        searchEvents();   // empty search just shows all upcoming events
    }
}

startPage();
</script>
</body>
</html>

