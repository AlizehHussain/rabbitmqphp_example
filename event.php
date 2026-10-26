<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Event Details</title>
</head>
<body>
// Everything visible is inside this div. "hidden" keeps it invisible
    // until JavaScript confirms the user is logged in
<div id="content" hidden>
// Navigation links back to the search page, home, and logout
    <p><a href="events.php">&larr; Back to events</a> | <a href="home.php">Home</a> | <a href="logout.php">Log out</a></p>

    <p id="status">Loading...</p>

    <div id="details" hidden>
        <h1 id="name"></h1>
        <p><strong>Date:</strong> <span id="date"></span></p>
        <p><strong>Venue:</strong> <span id="venue"></span></p>
        <p><strong>City:</strong> <span id="city"></span></p>
        <p><strong>Category:</strong> <span id="category"></span></p>
        //  Ticket link. target="_blank" opens it in a new tab.
           //  rel="noopener noreferrer" stops the Ticketmaster page from
            // controlling or seeing our page. Hidden unless the URL is valid
        <p><a id="ticketLink" target="_blank" rel="noopener noreferrer" hidden>Buy tickets on Ticketmaster</a></p>
    </div>
</div>

<script>
// Helper function: sends data to a PHP file as JSON and returns its JSON reply.
// "async" means it can wait for the network without freezing the page.
async function postJSON(url, body) {
    // fetch() sends an HTTP request to the given URL
    const response = await fetch(url, {
        method: "POST",                                   // POST sends data in the request body
        headers: { "Content-Type": "application/json" },  // tells PHP the body is JSON
        body: JSON.stringify(body)                        // converts the JS object into JSON text
    });
    // Convert the reply text from JSON back into a JS object
    return response.json();
}

// Checks if the user is logged in before showing anything.
// Returns true if the session is valid, otherwise redirects to login.
async function checkSession() {
    // The login page saved the token in the browser's sessionStorage
    const token = sessionStorage.getItem('session_token');
    // No token means the user never logged in, so send them to the login page
    if (!token) { window.location.href = "login.php"; return false; }
    try {
        // Ask sessionValidator.php (which asks the DB listener through RabbitMQ)
        // whether this token is still valid in the sessions table
        const data = await postJSON("sessionValidator.php", { typeRequest: 'validate_session', session_token: token });
        if (!data.success) {
            // Token is expired or fake: delete it from the browser and go to login
            sessionStorage.removeItem('session_token');
            window.location.href = "login.php";
            return false;
        }
        return true;   // session is valid
    } catch (e) {
        // If the server can't be reached, play it safe and send them to login
        window.location.href = "login.php";
        return false;
    }
}

// Loads one event's details and puts them on the page
async function loadEvent() {
    const status = document.getElementById('status');
    // Read the "id" from the page address, e.g. event.php?id=Z7r9jZ1AdFKdo
    // "|| ''" uses an empty string if there's no id in the address
    const eventId = new URLSearchParams(window.location.search).get('id') || "";

    // Regular expression check: only letters, numbers, _ and -, 1 to 64 characters.
    // Same rule as the handler and listener, so bad IDs are stopped early
    if (!/^[A-Za-z0-9_-]{1,64}$/.test(eventId)) {
        status.textContent = "Event not found.";
        return;
    }

    try {
        // Ask eventsHandler.php for this event. The handler checks the session,
        // then sends "get_event" through RabbitMQ to the DB listener
        const data = await postJSON("eventsHandler.php", {
            typeRequest: 'get_event',
            session_token: sessionStorage.getItem('session_token'),
            event_id: eventId
        });
        // If the request failed or no event came back, show the message and stop
        if (!data.success || !data.event) {
            status.textContent = data.message || "Event not found.";
            return;
        }

        const ev = data.event;
        // Put the event name in the browser tab
        document.title = ev.name;
        // Fill in each field. textContent inserts plain text only, so if an
        // event name contained HTML or script, it would show as text instead of running.
        // "||" provides a fallback when a field is empty
        document.getElementById('name').textContent = ev.name;
        document.getElementById('date').textContent = ev.date || "Date TBA";
        document.getElementById('venue').textContent = ev.venue || "TBA";
        document.getElementById('city').textContent = ev.city || "TBA";
        document.getElementById('category').textContent = ev.category || "Other";

        // Only show the ticket link if it's a real https web address.
        // This blocks things like "javascript:" links that could run code when clicked
        if (ev.url && ev.url.startsWith("https://")) {
            const link = document.getElementById('ticketLink');
            link.href = ev.url;
            link.hidden = false;
        }

        // Hide "Loading..." and show the details
        status.hidden = true;
        document.getElementById('details').hidden = false;
    } catch (e) {
        // Network error or bad reply from the server
        status.textContent = "Could not reach the server.";
    }
}

// This runs as soon as the page loads.
// (async () => { ... })() defines a function and immediately calls it,
// so we can use "await" at the top level
(async () => {
    // Only show the page and load the event if the user is logged in
    if (await checkSession()) {
        document.getElementById('content').hidden = false;
        loadEvent();
    }
})();
</script>
</body>
</html>
