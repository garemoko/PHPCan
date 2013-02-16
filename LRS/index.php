<?php

require 'Slim/Slim.php';
 
$app = new Slim();

//POST route
$app->post('/public', 'lrsPost');

//PUT route
$app->put('/public/:id', 'lrsPut');


$app->run();



function lrsPost(){
	$DBConnectionArray = returnDBConnectionArray();
	
    $inputStatement= file_get_contents("php://input");
	
	//decode the JSON as an associative array.
	$statementObj = json_decode($inputStatement, TRUE);
	
	//Stored
	$dateNow = new DateTime();
	$stored = $dateNow->format('Y-m-d H:i:s');
	$statementObj[0]['stored'] = $stored;
	
	//Add a statement ID if it doesn't already exist or if it's NULL. If it does exist, use the existing one. 
	$statementID;
	if (isset($statementObj[0]['id']))
	{
		$statementID = $statementObj[0]['id'];
	}
	else
	{
		$statementID = make_uuid();
		$statementObj[0]['id'] = $statementID;
	}

	
	
	//re-encode the statement
	$statement = json_encode($statementObj);
	

	$query = "INSERT INTO lrs_statements VALUES ('".$statementID."','".$statement."','".$stored."')";
	
	mysql_connect($DBConnectionArray["DBserver"],$DBConnectionArray["DBuser"], $DBConnectionArray["DBpassword"] );
	
	mysql_select_db($DBConnectionArray["DBname"]) or die( "Unable to select database");
	
	mysql_query($query);
	
	mysql_close();
}

function lrsPut(){
	$DBConnectionArray = returnDBConnectionArray();
	
    $statement= file_get_contents("php://input");
	$date = new DateTime();

	$query = "INSERT INTO lrs_statements VALUES ('".make_uuid()."','".$statement."','".$date->format('Y-m-d H:i:s')."')";
	
	mysql_connect($DBConnectionArray["DBserver"],$DBConnectionArray["DBuser"], $DBConnectionArray["DBpassword"] );
	
	mysql_select_db($DBConnectionArray["DBname"]) or die( "Unable to select database");
	
	mysql_query($query);
	
	mysql_close();
}


function make_uuid() {

    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

function returnDBConnectionArray()
{
	$array = array(
    "DBtype"           => "mysql",
	"DBserver"         => "cust-mysql-123-05",
	"DBname"           => "tincanapicouk_715945_db1",
	"DBuser"           => "utincan_715945_1",
	"DBpassword"       => "Taztaz1",
	);
	return $array;
}

?>
