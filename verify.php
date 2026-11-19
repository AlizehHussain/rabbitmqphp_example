<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Enter Login Code</title>
</head>
<body>

<h1>Check your email</h1>
<p>We sent a 6-digit login code to your email. It expires in 2 minutes.</p>

<!-- One box for the code. onsubmit returns false so the page doesn't reload -->
<form onsubmit="verifyCode(); return false;">
    <label for="code">Login code</label><br>
    <input type="text" id="code" maxlength="6" inputmode="numeric" autocomplete="one-time-code">
    <button type="submit">Verify</button>
</form>

<!-- Shows "Checking..." or an error message -->
<p id="status"></p>

<!-- If the code expired, they log in again to get a new one -->
<p><a href="login.php">Didn't get it or it expired? Log in again</a></p>

<script>
// login.php saved who is logging in. If it's missing, they skipped the login page.
const userId = sessionStorage.getItem("mfa_user_id");
if (!userId) {
    window.location.href = "login.php";
}

// Sends the code to verify_handler.php, which checks it through RabbitMQ
async function verifyCode() {
    const status = document.getElementById("status");
    const code = document.getElementById("code").value.trim();

    status.textContent = "Checking...";

    try {
        const response = await fetch("verify_handler.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ user_id: userId, code: code })
        });
        const data = await response.json();

        if (data.success) {
            // code was right: now we have a real session, same as the old login
            sessionStorage.setItem("session_token", data.session_token);
            sessionStorage.removeItem("mfa_user_id");

            // same as login.php used to do, go to a saved group invite, or to home
            const pendingInvite = sessionStorage.getItem("pending_invite");
            if (pendingInvite) {
                sessionStorage.removeItem("pending_invite");
                window.location.href = "join_group.html?token=" + encodeURIComponent(pendingInvite);
            } else {
                window.location.href = "home.php";
            }
        } else {
            status.textContent = data.message || "Invalid or expired code";
        }
    } catch (e) {
        status.textContent = "Could not reach the server.";
    }
}
</script>
</body>
</html>
