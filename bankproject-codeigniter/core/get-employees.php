<?php
session_start();
include('./helpers.php');
validatePostRequest([]);
include('./database.php');

$str = $_POST['query_string'] ?? '';
$status = $_POST['status'] ?? '%';
$status = $status == 'all' ? '%' : $status;
$COLUMN = $_POST['column'] ?? 'EmployeeID';
$ORDER = $_POST['order'] ?? 'ASC';


$sql = "SELECT EmployeeID, FirstName, LastName, TelephoneNumber, Email, JobTitleName, Status, JobID FROM dbo.[EmployeesList]
		WHERE (UserTypeName != 'Administrator') AND Status LIKE '$status' AND
		(EmployeeID LIKE '$str'
		OR Email LIKE '%$str%'
		OR FirstName LIKE '%$str%'
		OR LastName LIKE '%$str%'
		OR CONCAT(FirstName,' ',LastName) LIKE '%$str%') ORDER BY $COLUMN $ORDER";
$res = query($sql);
$employees = [];

while ($row = sqlsrv_fetch_array($res, 2)) {
	$row['TelephoneNumber'] = $row['TelephoneNumber'] == null ? '' : $row['TelephoneNumber'];
	$row['JobTitleName'] = ucwords($row['JobTitleName']);
	$row['Status'] = ucwords($row['Status']);
	$row['StatusColor'] = getStatusColor($row['Status']);
	$employees[] = $row;
}

echo json_encode($employees);
?>
