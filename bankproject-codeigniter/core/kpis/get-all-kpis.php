<?php
session_start();
include('../helpers.php');
validatePostRequest([]);
include('../database.php');

$res = sqlsrv_query($conn, "SELECT KPIID, KPITitle, CategoryName FROM dbo.KPIs JOIN KPICategories ON KPIs.KPICategoryID = KPICategories.KPICategoryID WHERE InLib = 1");
if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));

$kpis = [];
while ($row = sqlsrv_fetch_array($res, 2)) {
	$kpis[] = $row;
}

echo json_encode($kpis);

?>
