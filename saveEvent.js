// Shared "Save Event" button logic, used by events.php and event.php.
// Sends the logged-in user's session token and the event's ID to
//  Nick's save_pick_handler.php, which saves the pick through RabbitMQ
// and feeds his recommendation system.
// eventId = which event to save
// button = the Save button that was clicked (so we can change its text)
// msg = an element next to the button where errors are shown

async function saveEvent(eventId, button, msg) {
// Disable the button while saving so a double-click can't save twice
    button.disabled = true;
    button.textContent = "Saving...";
    msg.textContent = "";

    try {
// this posts  the token and event ID as JSON
        const response = await fetch("save_pick_handler.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                session_token: sessionStorage.getItem('session_token'),
                event_id: eventId
            })
        });
// The handler always replies with JSON, even on errors (with an HTTP error code),
// so we read the JSON either way to get its message
        const data = await response.json();

        if (data.success) {
// Saved: show "Saved" and leave the button disabled, since it's done
            button.textContent = "Saved";
            return;
        }
// Not saved: show the handler's reason (e.g. already saved, not logged in)
        msg.textContent = " " + (data.message || "Could not save event.");
    } catch (e) {
// if theres a  problem, or the reply wasn't valid JSON
        msg.textContent = " Could not reach the server.";
    }

 // if it failes, turn the button back on so the user can try again
    button.disabled = false;
    button.textContent = "Save Event";
}
