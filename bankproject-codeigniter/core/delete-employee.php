<?php
session_start();
include('./helpers.php');
validatePostRequest(["EmployeeID"]);
include('./database.php');

$sql = "SELECT UserTypeName FROM Employees
		JOIN UserTypes ON UserTypes.UserTypeID = Employees.UserTypeID
		WHERE EmployeeID = '" . $_SESSION['EmployeeID'] . "';";
$res = sqlsrv_query($conn, $sql);
if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));

$employee = sqlsrv_fetch_array($res, 2);
if (!in_array(strtolower($employee['UserTypeName']), ['authorized user', 'administrator']) ) {
	die(json_encode(['status' => true, 'message' => "Only Admin can perform this action."]));
}



if (sqlsrv_query($conn, "DELETE FROM dbo.Employees WHERE ")) {
	die(json_encode(['status' => true, 'message' => "Deleted successfully", 'requirement_row_id' => $row_id]));
}
else {
	die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
}
?>