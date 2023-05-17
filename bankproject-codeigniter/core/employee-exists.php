<?php
include("database.php");


$sql = "SELECT EmployeeID FROM Employees WHERE Email = '" . $_POST['Email'] . "'";
$res = sqlsrv_query($conn, $sql);
if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
$employee = sqlsrv_fetch_array($res, 2);

if ($employee) {
	die("false");
}

die("true");
