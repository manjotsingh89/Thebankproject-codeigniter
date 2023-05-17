<?php
session_start();
include('../helpers.php');
validatePostRequest(['ProjectName']);
include('../database.php');


$projectName = $_POST['ProjectName'];
$inLib = isset($_POST['InLib']) ? 1 : 0;

$sql = "INSERT INTO dbo.Projects (ProjectName, InLib) VALUES ('" . $projectName . "', '" . $inLib . "'); SELECT SCOPE_IDENTITY() AS ID;";
$res = sqlsrv_query($conn, $sql);

if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));

echo json_encode(['status' => true, 'project' => ['ProjectID' => getLastInsertID($res), 'ProjectName' => $projectName]]);
?>
