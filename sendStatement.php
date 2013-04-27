<?PHP
function buildStatement($actorName, $actorEmail, $verbId, $verbDisplay, $activityID, $activityName, $activityDesc) {
	$statement = array( 
		'actor' => array(
			'name' => $actorName, 
			'mbox' => 'mailto:' . $actorEmail, 
			'objectType' => 'Agent',
		), 
		'verb' => array(
			'id' => $verbId,
			'display' => array(
				'en-US' => $verbDisplay,
				'en-GB' => $verbDisplay
				),
			),
		'object' => array(
			'id' => $activityID, 
			'definition' => array(
				'name' => array(
					'en-US' => $activityName,
					'en-GB' => $activityName
				), 
				'description' => array(
					'en-US' => $activityDesc, 
					'en-GB' => $activityDesc
				), 
			), 
		), 
	);

	return $statement;

}

function make_request($data, $url, $basicLogin, $basicPass) {
	
	$streamopt = array(
		'ssl' => array(
			'verify-peer' => false, 
			), 
		'http' => array(
			'method' => 'POST', 
			'ignore_errors' => false, 
			'header' => array(
				'Authorization: Basic' . base64_encode( $basicLogin . ':' . $basicPass), 
				'Content-Type: application/json', 
				'Accept: application/json, */*; q=0.01',
				'X-Experience-API-Version: 1.0.0'
			), 
			'content' => myJson_encode($data), 
		), 
	);
	$context = stream_context_create($streamopt);

	$stream = fopen($url . 'statements', 'rb', false, $context);
	$ret = stream_get_contents($stream);
	$meta = stream_get_meta_data($stream);
	if ($ret) {
		$ret = json_decode($ret);
	}
	return array($ret, $meta);
}

function myJson_encode($str)
{
	return str_replace('\\/', '/',json_encode($str));
}

//Endpoint details
$basicLogin = 'username';
$basicPass = 'password';

//NOTE: do not include "statements" as part of the endpoint URL. This is added later.
$endpoint = 'https://cloud.scorm.com/ScormEngineInterface/TCAPI/public/';
 
 
//statement vars
$actorName = 'Example McExampleson';
$actorEmail = 'xapi@example.com'; 

$verbId = 'http://adlnet.gov/expapi/verbs/created';
$verbDisplay = 'created';

$activityURL = 'http://example.adlnet.gov/xapi/example/activity';
$activityName = 'Example activity'; 
$activityDesc = 'An example activity for example purposes';

//build the statement
$statement = buildStatement($actorName, $actorEmail, $verbId, $verbDisplay, $activityURL, $activityName, $activityDesc);

//send the statement - returns the response and metadata
list($resp, $meta) = make_request($statement, $endpoint, $basicLogin, $basicPass);

echo '<p>response: ' . $resp[0] . '</p>'; //This is the statement ID
echo '<p>meta: ' . myJson_encode($meta) . '</p>';

?>
