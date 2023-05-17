<?php
session_start();
include('../helpers.php');
validatePostRequest([]);
include('../database.php');
$res = sqlsrv_query($conn, "SELECT ProjectID, ProjectName, InLib FROM dbo.Projects WHERE InLib = 1");

if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));

$projects = [];
while ($row = sqlsrv_fetch_array($res, 2)) {
	$projects[] = $row;
}

print_r(json_encode($projects));
?>
