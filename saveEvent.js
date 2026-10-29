// Saves an event to the user's picks. Used by the Save buttons on home.php and event.php.
// eventId = which event, button = the Save button clicked, msg = the span next to it for errors
async function saveEvent(eventId, button, msg) {
    button.disabled = true;           // stops double clicks while it's saving
    button.textContent = "Saving...";
    msg.textContent = "";

    try {
        // Send the token and event ID to Nick's handler, which passes it through RabbitMQ
        const response = await fetch("save_pick_handler.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                session_token: sessionStorage.getItem('session_token'),
                event_id: eventId
            })
        });
        const data = await response.json();

        if (data.success) {
            button.textContent = "Saved";
            // Pop-up. The listener sends "Event saved successfully" or "Event already saved"
            alert(data.message || "Event saved!");
        } else {
            // It failed, so let them try again and show why
            button.disabled = false;
            button.textContent = "Save Event";
            msg.textContent = " " + (data.message || "Could not save event.");
        }
    } catch (e) {
        button.disabled = false;
        button.textContent = "Save Event";
        msg.textContent = " Could not reach the server.";
    }
}
