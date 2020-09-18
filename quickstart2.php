<?php
require __DIR__ . '/vendor/autoload.php';
include 'template/header.php';

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Google Calendar API PHP Quickstart');
    $client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
    $client->setAuthConfig(__DIR__ . '/credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = 'token.json';

    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}


// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Calendar($client);

// Print the next 10 events on the user's calendar.
$calendarId = 'primary';
$optParams = array(
    'maxResults' => 10,
    'orderBy' => 'startTime',
    'singleEvents' => true,
    'timeMin' => date('c'),
);
$results = $service->events->listEvents($calendarId, $optParams);
$events = $results->getItems();

if (empty($events)) {
    ?>
    <h1>
    <?php
    print "No upcoming events found.\n";
    ?>
    </h1>
    <?php
} else {
    ?>
    <div class="col-12" id="messages-success-fail" style="margin-top: 50px;">
        <?php
        if(isset($_POST['submit'])) {
            if (count($_POST) >= 4) {
                ?>
                <div class="alert alert-success" role="alert">Event created</div>
                <?php
            }else{
                ?>
                <div class="alert alert-danger" role="alert">Event not created</div>
                <?php
            }
        }
        ?>
    </div>
    <?php
    print "<h1 class='text-center'>Upcoming events</h1>";
    echo "<table class='table'>
  <thead>
    <tr>
      <th scope='col'>Summary</th>
      <th scope='col'>Description</th>
      <th scope='col'>Date and Time</th>
    </tr>
  </thead>
  <tbody>";
    foreach ($events as $event) {
        $start = $event->start->dateTime;
        if (empty($start)) {
            $start = $event->start->date;
        }
        print("<tr>");
        printf("%s %s %s\n", "<td>" . $event->getSummary() . "</td>", "<td>" . $event->getDescription() . "</td>", "<td>" . $start . "</td>");
        print("</tr>");
    }
    echo "</tbody>
</table>";
}
echo "<br><button type='submit' class='btn btn-primary' name='submit'><a href='index.php' style='color: white;'>Back to form</a></button>";
include 'template/footer.php';