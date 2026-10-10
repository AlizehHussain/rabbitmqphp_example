<?php
?>
<!DOCTYPE html>

<h3>Login</h3>
<form onsubmit="return validate(this)" method="POST">
<div>
    <!-- enter the email or username -->
    <label for="loginID">Email or Username</label><br>
    <input type="text" id="loginID" name="loginID" required><br>
    
    <!-- enter the password -->    
    <label for="pwd">Password</label><br>
    <input type="password" id="pwd" name="password" required><br>

    <!-- adding button for login -->
    <button type="submit">Login</button>
</div>
</form>

<script> 
//validating the form values
    function validate(form) {
        //getting values via valiate(this)
        let email = form.loginID.value.trim();
        let password = form.password.value;

        //checking if the email is empty
        if (email === "") 
        {
            alert("Email or username must not be empty.");
            return false;
        }

        //validating the entered email is in the correct format
        if (email.includes("@")) {
            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email))
            {
                alert("Please enter a valid email address/username and password.");
                return false;
            }
        }
        // validating the username is in the correct format
        else
        {
            let usernameRegex = /^[a-zA-Z0-9_-]{3,30}$/;
            if (!usernameRegex.test(email))
            {
                alert("Please enter a valid email address/username and password.");
                return false;
            }
        }
        //checking if the password is empty
         if (password === "") 
        {
            alert("Password must not be empty.");
            return false;
        }
        // password must be atleast 8 characters
        if (password.length < 8)
        {
            alert("Please enter a valid email address/username and password.");
            return false;
        }

        sendLogin(email, password);

        return false;
    }

    // This function sends the input to the login handler and receives a response
    async function sendLogin(text, password) {
        const response = await fetch("login_handler.php", {
            method: "POST",
            headers:{
            //header tells the server the format of the request's body
            "Content-Type": "application/json"},
            // converts the login id and password into json to use as request body
            body: JSON.stringify({loginID: text, password: password})
        });
        // waits for the server reply 
        const data = await response.json();

        // checks whether the login details are correct
        if (data.success)
        {
            // if the login details are correct, it stores token into a session storage
            sessionStorage.setItem("sessionToken", data.session_token)
            alert("You have successfully logged in")
        }
        else{
            // else tells the user that the login details were incorrect, and doesn't store them
            alert("Oops, looks like you couldn't get in")
        }
        
    }
</script>