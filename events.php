<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Browse Events</title>
</head>
<body>
<div id="content" hidden>
    <p><a href="home.php">Home</a> | <a href="logout.php">Log out</a></p>
    <h1>Browse Events</h1>

    <form id="searchForm">
        <input type="text" id="keyword" placeholder="Artist, event, or venue">
        <input type="text" id="city" placeholder="City">
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

    <p id="status"></p>
    <ul id="results"></ul>
</div>

<script src="saveEvent.js"></scripts>
<script>
async function postJSON(url, body) {
    const response = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(body)
    });
    return response.json();
}

async function checkSession() {
    const token = sessionStorage.getItem('session_token');
    if (!token) { window.location.href = "login.php"; return false; }
    try {
        const data = await postJSON("sessionValidator.php", { typeRequest: 'validate_session', session_token: token });
        if (!data.success) {
            sessionStorage.removeItem('session_token');
            window.location.href = "login.php";
            return false;
        }
        return true;
    } catch (e) {
        window.location.href = "login.php";
        return false;
    }
}

async function searchEvents() {
    const status = document.getElementById('status');
    const list = document.getElementById('results');
    status.textContent = "Searching...";
    list.innerHTML = "";
    try {
        const data = await postJSON("eventsHandler.php", {
            typeRequest: 'search_events',
            session_token: sessionStorage.getItem('session_token'),
            keyword: document.getElementById('keyword').value.trim(),
            city: document.getElementById('city').value.trim(),
            category: document.getElementById('category').value
        });
        if (!data.success) { status.textContent = data.message || "Search failed."; return; }
        if (data.events.length === 0) { status.textContent = "No events found."; return; }

        status.textContent = data.events.length + " event(s) found";
        for (const ev of data.events) {
            const li = document.createElement('li');
            const link = document.createElement('a');
            link.href = "event.php?id=" + encodeURIComponent(ev.event_id);
            link.textContent = ev.name;
            li.appendChild(link);
            li.appendChild(document.createTextNode(
                " — " + (ev.date || "Date TBA") + ", " + (ev.venue || "") + (ev.city ? ", " + ev.city : "")
            ));
// Save button for this event. Ties in with  Nick's recommendation system
            const saveBtn = document.createElement('button');
            saveBtn.textContent = "Save Event";
// Small area next to the button for error messages
            const saveMsg = document.createElement('span');
// When clicked, save specific event
            saveBtn.addEventListener('click', () => saveEvent(ev.event_id, saveBtn, saveMsg));
            li.appendChild(document.createTextNode(" "));   // space before the button
            li.appendChild(saveBtn);
            li.appendChild(saveMsg);

            list.appendChild(li);
        }
    } catch (e) {
        status.textContent = "Could not reach the server.";
    }
}

document.getElementById('searchForm').addEventListener('submit', (e) => {
    e.preventDefault();
    searchEvents();
});

(async () => {
    if (await checkSession()) {
        document.getElementById('content').hidden = false;
        searchEvents();
    }
})();
</script>
</body>
</html>
