#!/bin/bash

#Stops if a command fails or variable is missing. I just dont want it to continue if theres ticketmaster data errors
set -euo pipefail

#loading our api, ticketmaster.env contains my API key, now i dont gotta write out the entire key
source /etc/eventplanner/ticketmaster.env

#this is where our data is saved
OUTPUT_DIR="/opt/eventplanner" #the directory where the data is stored
OUTPUT_FILE="$OUTPUT_DIR/events.json" #the file where the data is stored

mkdir -p "$OUTPUT_DIR"

echo "Starting Ticketmaster update: $(date)"

#Download events from ticketmaster
#first saving to temp file so a failed request wont ruin the entire thing lol
TEMP_FILE=$(mktemp "$OUTPUT_DIR/events.XXXXXX")
trap 'rm -f "$TEMP_FILE"' EXIT #trap basically registers a command to execute when the script exits, doesnt matter if it succeeds or fails

#now sending an http request to ticketmaster, curl is the best way to retrieve API data from a bash script. otherwise id need a separate programming library
curl --fail --silent --show-error \ #treats http response as failures, hides unnecessary progress, and still displays the error
	--retry 3 \
	--max-time 30 \
	--get \
	"https://app.ticketmaster.com/discovery/v2/events.json" \
	--data-urlencode "apikey=$TM_API_KEY" \ #safely encodes my API parameters
	--data-urlencode "city=New York" \
	--data-urlencode "countryCode=US" \
	--data-urlencode "size=20" \
	--output "$TEMP_FILE" #there is where my output is saved

#jq is a command-line tool for processing JSON, i access ticketmaster's event data, verify its structure as an array
#put it in dev/null bc idc about the output, i just want to know if it succeeds
#-e returns a failure status if the condition is false
jq -e '._embedded.events | type == "array"' "$TEMP_FILE" > /dev/null

#now itll replace existing data after a successful validation
mv "$TEMP_FILE" "$OUTPUT_FILE"

echo "Ticketmaster data updated successfully: $(date)"
echo "Events saved: $(jq '._embedded.events | length' "$OUTPUT_FILE")" #counting the downloaded content
