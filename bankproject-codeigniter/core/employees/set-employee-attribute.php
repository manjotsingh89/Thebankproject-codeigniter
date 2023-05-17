<?php
session_start();
include('../helpers.php');
validatePostRequest(['EmployeeID', 'value', 'column']);
include('../database.php');

$res = query("SELECT * FROM Employees WHERE EmployeeID = " . $_POST['EmployeeID']);
$employee = sqlsrv_fetch_array($res, 2);

if($_POST['column'] == 'EmployeeStatusID' && $_POST['value'] == 3){
	$sql = "UPDATE Jobs SET JobStatusID = 4 WHERE FilledByEmpID = " . $_POST['EmployeeID'];
	$res = sqlsrv_query($conn, $sql);
	if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
} elseif ($_POST['column'] == 'EmployeeStatusID') {
	die(json_encode(['status' => false, 'message' => 'Not Allowed']));
}

if ($_POST['column'] == 'CurrentSupervisorID' && $_SESSION['UserTypeName'] != 'administrator') {
	die(json_encode(['status' => false, 'message' => 'Only Admin can perform this action']));
}

if (($_POST['column'] == 'NextReviewDate' || $_POST['column'] == 'EmployeeStatusID') && $_SESSION['EmployeeID'] != $employee['CurrentSupervisorID']) {
	die(json_encode(['status' => false, 'message' => 'Only supervisor can perform this action']));
}

$sql = "UPDATE dbo.employees SET " . $_POST['column'] . " = '" . $_POST['value'] . "' WHERE EmployeeID = " . $_POST['EmployeeID'];
$res = sqlsrv_query($conn, $sql);
if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));


die(json_encode(['status' => true, 'message' => $_POST['column'] . " Updated"]));
?>