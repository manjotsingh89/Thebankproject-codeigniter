<?php
session_start();
include('./helpers.php');
validatePostRequest(['JobID', 'value', 'column']);
include('./database.php');

$content = '';

if (is_array($_POST['column'])) {
	foreach ($_POST['column'] as $index => $column) {
		$value = $_POST['value'][$index];
		$content .= "$column = '$value', ";
	}
} else {
	$content = $_POST['column'] . " = '" . $_POST['value'] . "' ";

	if ($_POST['column'] == 'FilledByEmpID') {
		$sql = "SELECT * FROM dbo.[EmployeesList] WHERE EmployeeID = '" . $_POST['value'] . "';";
		$res = sqlsrv_query($conn, $sql);
		if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
		$employee = sqlsrv_fetch_array($res, 2);
		if ($employee['CurrentSupervisorID'] != $_SESSION['EmployeeID']) die(json_encode(['status' => false, 'message' => 'Only supervisor can perform this action']));
		if (strtolower($employee['Status']) == 'retired') die(json_encode(['status' => false, 'message' => 'This employee is retired']));

		$sql = "SELECT JobStatusID FROM dbo.[Jobs] WHERE JobID = '" . $_POST['JobID'] . "';";
		$res = sqlsrv_query($conn, $sql);
		if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
		$job = sqlsrv_fetch_array($res, 2);
		if ($job['JobStatusID'] != 2) die(json_encode(['status' => false, 'message' => 'This job is not open']));

		$sql = "UPDATE dbo.Employees SET EmployeeStatusID = 1 WHERE EmployeeID = '" . $_POST['value'] . "';";
		$res = sqlsrv_query($conn, $sql);
		if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
	} elseif ($_POST['column'] == 'JobStatusID') {
		if (in_array((int)$_POST['value'], [3, 4])) die(json_encode(['status' => false, 'message' => 'Not Allowed']));
		$res = sqlsrv_query($conn, "SELECT * FROM Jobs WHERE JobID = " . $_POST['JobID']);
		if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
		$job = sqlsrv_fetch_array($res, 2);
		if (!$job) die(json_encode(['status' => false, 'message' => 'Invalid Job ID']));
		if ($job['FilledByEmpID'] != NULL) die(json_encode(['status' => false, 'message' => 'Not Allowed']));
	}
}

$content = rtrim($content, ', ');

$sql = "UPDATE dbo.Jobs SET " . $content . " WHERE JobID = " . $_POST['JobID'] . ";";
$res = sqlsrv_query($conn, $sql);
if ($res) {
	if (sqlsrv_rows_affected($res) == 0) {
		die(json_encode(['status' => false, 'message' => 'Only draft Jobs can be updated.']));
	} else {
		die(json_encode(['status' => true, 'message' => "Job Updated"]));
	}
}
else {
	die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
}

?>