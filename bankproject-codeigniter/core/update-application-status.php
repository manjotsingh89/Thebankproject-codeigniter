<?php

session_start();
if ($_SESSION['UserTypeName'] != 'administrator') {
	header('location: index.php');exit();
}

require_once("database.php");
include('helpers.php');

if (isset($_GET['ApplicationID'])) {
	if ($_GET['ApplicationID'] == null) {
		header('location: /applications.php');exit();
	}

	$ApplicationID = $_GET['ApplicationID'];
	query("UPDATE Applications SET ApplicationStatus = 'rejected' WHERE ApplicationID = '$ApplicationID'");
	header('location: /applications.php');exit();
}

validatePostRequest(['ApplicationID']);
$ApplicationID = $_POST['ApplicationID'];


$res = query("SELECT ApplicationStatus FROM Applications WHERE ApplicationID = $ApplicationID");
$status = sqlsrv_fetch_array($res, 1)[0];
if ($status != 'pending approval') {
	header('location: /applications.php');exit();
}

query("DELETE FROM ApplicationInterviewers WHERE ApplicationID = $ApplicationID");
if ($_POST['Interviewers-type'] == 'custom') {
	if (count($_POST['employees']) < 3) {
		header('location: /applications.php');exit();
	}
	$employees = $_POST['employees'];
	
	foreach ($employees as $employee) {
		query("INSERT INTO ApplicationInterviewers (ApplicationID, InterviewerID) VALUES ($ApplicationID, $employee)");
	}
} else {
	$res = query("SELECT EmployeeID FROM Employees WHERE Interviewer = 1");
	while($employee = sqlsrv_fetch_array($res, 1)){
		$employee = $employee[0];
		query("INSERT INTO ApplicationInterviewers (ApplicationID, InterviewerID) VALUES ($ApplicationID, $employee)");
	}
}

query("UPDATE Applications SET ApplicationStatus = 'approved' WHERE ApplicationID = $ApplicationID");

header('location: /applications.php');exit();