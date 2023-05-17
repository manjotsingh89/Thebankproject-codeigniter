<?php
session_start();
include('../helpers.php');
validatePostRequest(['KPITitle', 'KPICategoryID']);
include('../database.php');


$KPITitle = $_POST['KPITitle'];
$KPICategoryID = $_POST['KPICategoryID'];
$inLib = isset($_POST['InLib']) ? 1 : 0;

$sql = "INSERT INTO dbo.KPIs (KPITitle, InLib, KPICategoryID) VALUES ('$KPITitle', '$inLib', '$KPICategoryID'); SELECT SCOPE_IDENTITY() AS ID;";
$res = sqlsrv_query($conn, $sql);

if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
$res2 = sqlsrv_query($conn, "SELECT TOP (1) [KPICategoryID],[CategoryName] FROM [dbo].[KPICategories] WHERE KPICategoryID = '$KPICategoryID'");
$c = sqlsrv_fetch_array($res2, 2);
echo json_encode(['status' => true, 'kpi' => [
	'KPIID' => getLastInsertID($res),
	'KPITitle' => $KPITitle,
	'KPICategoryID' => $KPICategoryID,
	'CategoryName' => $c['CategoryName'],
]]);
?>
