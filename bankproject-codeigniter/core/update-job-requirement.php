<?php
session_start();
include('./helpers.php');
validatePostRequest(['JobRequirementID', 'column', 'value']);
include('./database.php');

$column = $_POST['column'];
$JobRequirementID = $_POST['JobRequirementID'];
$value = $_POST['value'];

$sql = "UPDATE dbo.JobRequirements SET $column = '$value' WHERE JobRequirementID = $JobRequirementID";
$res = sqlsrv_query($conn, $sql);
if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));

echo json_encode(['status' => true]);


?>