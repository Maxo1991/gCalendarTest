<?php
require __DIR__ . '/vendor/autoload.php';
include 'template/header.php';

$name = $_POST['example-name-input'];
$phone = $_POST['example-tel-input'];
$email = $_POST['example-email-input'];
$date = $_POST['datepickerfrom'];
$startTime = $_POST['timepickerstart'];
$endTime = $_POST['timepickerend'];

$proba = explode("/", $date);
$newDate = "{$proba[2]}-{$proba[0]}-{$proba[1]}";
$newStartTime = $startTime . ":00";
$newEndTime = $endTime . ":00";
$completeDateStart = $newDate . "T" . $newStartTime;
$completeDateEnd = $newDate . "T" . $newEndTime;

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Google Calendar API PHP Quickstart');
    $client->setScopes(Google_Service_Calendar::CALENDAR);
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

// Refer to the PHP quickstart on how to setup the environment:
// https://developers.google.com/calendar/quickstart/php
// Change the scope to Google_Service_Calendar::CALENDAR and delete any stored
// credentials.

$event = new Google_Service_Calendar_Event(array(
    'summary' => "Event by $name",
    'description' => "$name create event, \n Phone number: $phone",
    'start' => array(
        'dateTime' => $completeDateStart,
        'timeZone' => 'Europe/Belgrade',
    ),
    'end' => array(
        'dateTime' => $completeDateEnd,
        'timeZone' => 'Europe/Belgrade',
    ),
    'recurrence' => array(
        'RRULE:FREQ=DAILY;COUNT=1'
    ),
    'attendees' => array(
        array('email' => $email),
    ),
    'reminders' => array(
        'useDefault' => FALSE,
        'overrides' => array(
            array('method' => 'email', 'minutes' => 30),
            array('method' => 'email', 'minutes' => 15),
        ),
    ),
));
$calendarId = 'primary';
$event = $service->events->insert($calendarId, $event);
?>
    <div class="alert alert-success" role="alert">
<?php
print('Event created');
?>
    </div>
<?php
echo "<br><button type='submit' class='btn btn-primary' name='submit'><a href='quickstart2.php' style='color: white;'>Upcoming events</a></button>";

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);
try {
    //Server settings
    $mail->SMTPDebug = 0;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'testprezimetestime@gmail.com';                     // SMTP username
    $mail->Password   = 'test12TEST_';                               // SMTP password
//    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->SMTPSecure = 'tls';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom('testprezimetestime@gmail.com');
    $mail->addAddress($email);

    // Name is optional
    $mail->addReplyTo('testprezimetestime@gmail.com', 'TestIme TestPrezime');

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = "Event from $name";
    $mail->Body    = "<h1> Hello $name, </h1>
                        <p>Thanks for creating the event.</p>
                        <p>Event detail </p>
                        <p>Date: $newDate</p>
                        <p>Start time: $startTime</p>
                        <p>End time: $endTime</p>
                        <p>Contact number: $phone</p><br>
                        <p>Best Regards,</p>
                        <p>Test</p>";

    $mail->send();
} catch (Exception $e) {

}

include 'template/footer.php';