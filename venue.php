<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Venue Details</title>
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

    <!-- The venue details, hidden until the data arrives -->
    <div id="details" hidden>
        <h1 id="venue"></h1>
        <p><strong>City:</strong> <span id="city"></span></p>

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
        <p><strong>Global Rating: </strong><span id="globalAvg">...</span></p>

        <form id="review-form">
            <textarea id="comment" name="comment" placeholder="Your review" required></textarea>
            <button type="submit" id="reviewBtn">Submit</button>
            <p id="review-status"></p>
        </form>

        <h2>Venue Reviews</h2>
        <div id="reviews-list"></div>
    </div>
</div>

<script>
const token = sessionStorage.getItem('session_token');
let clicked = false;
let rating = 0;
const form = document.getElementById('review-form');
const list = document.getElementById('reviews-list');

// Adds one review to the list: "username said this <comment>"
function addReview(r) {
    const p = document.createElement('p');
    const name = document.createElement('strong');
    name.textContent = r.username + ' said this ';
    p.append(name, r.comment);   // plain text only, so nobody can inject code
    list.appendChild(p);
}

// Sends data to one of our PHP files and returns the PHP file's answer
async function sendRequest(url, data) {
    const response = await fetch(url, {
        method: "POST", // send data to the server
        headers: { "Content-Type": "application/json" }, // tell PHP we're sending JSON
        body: JSON.stringify(data) // turn our data into JSON text
    });
    return response.json(); // turn PHP's JSON answer back into an object
}

// Shows the average text, or "No reviews yet" when there are no ratings
function showAverage(average) {
    const avg = Math.round(average * 100) / 100;
    document.getElementById('globalAvg').textContent =
        Number.isNaN(avg) ? "No reviews yet" : avg + "/5";
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

// Loads one venue's rating and reviews and shows them on the page
async function loadVenue() {
    const status = document.getElementById('status');

    // Read the venue and city from the address, e.g. venue.php?venue=Prudential%20Center&city=Newark
    const params = new URLSearchParams(window.location.search);
    const venue = params.get('venue');
    const city = params.get('city') || '';

    // No venue in the address means there's nothing to show.
    if (!venue) {
        status.textContent = "Venue not found.";
        return;
    }

    // Fetches this venue's reviews and rebuilds the list
    async function loadReviews() {
        const res = await fetch(
            `getVenueReviews.php?venue=${encodeURIComponent(venue)}&city=${encodeURIComponent(city)}`
        );
        const reviewData = await res.json();
        if (!reviewData.success) return;
        list.innerHTML = '';
        reviewData.reviews.forEach(addReview);
    }

    try {
        // Gets the venue's average rating (clicked is false, so nothing is saved)
        const data = await sendRequest("venue_handler.php", {
            venue: venue,
            city: city,
            sessionToken: token,
            clicked: clicked,
            rating: rating
        });

        // If something went wrong on the server
        if (!data.success) {
            status.textContent = data.message || "Venue not found.";
            return;
        }

        showAverage(data.average);

        // The venue and city come straight from the address
        document.title = venue;
        document.getElementById('venue').textContent = venue;
        document.getElementById('city').textContent = city || "TBA";

        loadReviews();

        // Saves a rating whenever a star is clicked
        document.querySelectorAll('.rating input').forEach(function (input) {
            input.addEventListener('change', async function () {
                clicked = true;
                const r = await sendRequest("venue_handler.php", {
                    sessionToken: token,
                    clicked: clicked,
                    venue: venue,
                    city: city,
                    rating: input.value
                });
                if (r.success) {
                    showAverage(r.average);
                } else {
                    document.getElementById('globalAvg').textContent = r.message || "Could not save rating.";
                }
            });
        });

        // Saves a review when the form is submitted
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const comment = document.getElementById('comment').value.trim();
            const r = await sendRequest("venueReview_handler.php", {
                sessionToken: token,
                venue: venue,
                city: city,
                review: comment
            });
            if (r.success) {
                document.getElementById('review-status').textContent = '';
                form.reset();
                loadReviews();
            } else {
                document.getElementById('review-status').textContent = r.message || "Could not save review.";
            }
        });

        // Hide "Loading..." and show the details
        status.hidden = true;
        document.getElementById('details').hidden = false;
    } catch (e) {
        console.error(e);
        status.textContent = "Error: " + e.message;
    }
}

// Runs once when the page loads: check login, then show the page and load the venue
async function startPage() {
    const loggedIn = await checkLogin();
    if (loggedIn) {
        document.getElementById('content').hidden = false;
        loadVenue();
    }
}

startPage();
</script>
</body>
</html>
