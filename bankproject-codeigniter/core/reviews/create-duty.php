<?php
session_start();
include('../helpers.php');
validatePostRequest(['DutyName']);
include('../database.php');


$dutyName = $_POST['DutyName'];
$inLib = isset($_POST['InLib']) ? 1 : 0;

$sql = "INSERT INTO dbo.Duties (DutyName, InLib) VALUES ('" . $dutyName . "', '" . $inLib . "'); SELECT SCOPE_IDENTITY() AS ID;";
$res = sqlsrv_query($conn, $sql);

if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));

echo json_encode(['status' => true, 'duty' => ['DutyID' => getLastInsertID($res), 'DutyName' => $dutyName]]);
?>
