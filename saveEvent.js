// When a user clicks Save, we send their session token and the
// event's ID to Nick's save_pick_handler.php. That file sends a
// "save_pick" request through RabbitMQ to the database listener,
// which stores the pick. Saved picks are what Nick's
// recommendation system uses.

// eventId = the ID of the event to save
// button = the Save button that was clicked, so we can change its text
// msg = the spot next to the button where we show error messages
async function saveEvent(eventId, button, msg) {
// Turn the button off while saving, so clicking twice can't accidentally send two save requests
    button.disabled = true;
    button.textContent = "Saving...";
    msg.textContent = "";

    try {
// Send the token and event ID to Nick's handler as JSON
        const response = await fetch("save_pick_handler.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                session_token: sessionStorage.getItem('session_token'),
                event_id: eventId
            })
        });

// Nick's handler always answers in JSON, even when something goes wrong, so we read the answer either way to get its message
        const data = await response.json();

        if (data.success) {
            // It saved. Show "Saved" and leave the button off,
            // since there's nothing left to do
            button.textContent = "Saved";
            return;
        }

        // If it didn't save. Show the reason the server gave us,
        // for example if the event was already saved
        msg.textContent = " " + (data.message || "Could not save event.");
    } catch (e) {
 // The server couldn't be reached, or sent back something that wasn't JSON
        msg.textContent = " Could not reach the server.";
    }

    // This is for if saving failed. The button turns back on
    // to let the user try again
    button.disabled = false;
    button.textContent = "Save Event";
}
