<?php
session_start();
include('../helpers.php');
validatePostRequest(['employeeId', 'nextReviewDate']);
include('../database.php');

$sql = "UPDATE dbo.employees SET NextReviewDate = '" . $_POST['nextReviewDate'] . "' WHERE EmployeeID = " . $_POST['employeeId'];

if (sqlsrv_query($conn, $sql)) {
	die(json_encode(['status' => true]));
}
else {
	die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
}

?>