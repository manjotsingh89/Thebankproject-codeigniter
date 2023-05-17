<?php
//utilities
function getStatusColor($status){
	$colors = ["Applicant" => "warning", "Active" => "success", "Retired" => "danger", 'Offer Sent' => 'success', 'KIV' => 'success'];

	return $colors[$status];
}

function getDateFromObj($obj){
	$date   = new DateTime($obj); //this returns the current date time

	return json_encode($date);
}

function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' )
{
    $datetime1 = date_create($date_1);
    $datetime2 = date_create($date_2);
    $interval = date_diff($datetime1, $datetime2);

    return $interval->format($differenceFormat);
}

function getLastInsertID($id) {
     sqlsrv_next_result($id);
     sqlsrv_fetch($id);
     return sqlsrv_get_field($id, 0);
}

function validatePostRequest($attributes = [], $auth = true) {
	if ($auth && !isset($_SESSION['EmployeeID'])) die(json_encode(['status' => false, 'message' => 'Authorization Failed']));

	foreach ($attributes as $attribute) {
		if (!isset($_POST[$attribute]) || $_POST[$attribute] == null) {
			die(json_encode(['status' => false, 'message' => "$attribute field is required"]));
		}
	}
}

function addJobRequirement($conn, $jobID, $column, $value) {
	$sql = "INSERT INTO dbo.JobRequirements (JobID, $column) VALUES ('$jobID', '$value');SELECT SCOPE_IDENTITY() AS ID;";
	$res = sqlsrv_query($conn, $sql);
	if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
	return getLastInsertID($res);
}

function get_client_ip() {
    // $ipaddress = '';
    // if (getenv('HTTP_CLIENT_IP'))
    //     $ipaddress = getenv('HTTP_CLIENT_IP');
    // else if(getenv('HTTP_X_FORWARDED_FOR'))
    //     $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    // else if(getenv('HTTP_X_FORWARDED'))
    //     $ipaddress = getenv('HTTP_X_FORWARDED');
    // else if(getenv('HTTP_FORWARDED_FOR'))
    //     $ipaddress = getenv('HTTP_FORWARDED_FOR');
    // else if(getenv('HTTP_FORWARDED'))
    //    $ipaddress = getenv('HTTP_FORWARDED');
    // else if(getenv('REMOTE_ADDR'))
    //     $ipaddress = getenv('REMOTE_ADDR');
    // else
    //     $ipaddress = 'UNKNOWN';
    // return $ipaddress;
    return $_SERVER['REMOTE_ADDR'];
}

?>