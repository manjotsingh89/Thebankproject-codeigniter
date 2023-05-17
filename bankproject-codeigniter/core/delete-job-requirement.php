<?php
session_start();
include('./helpers.php');
validatePostRequest(["JobRequirementID"]);
include('./database.php');

$sql = "SELECT * FROM dbo.JobRequirements WHERE JobRequirementID = " . $_POST['JobRequirementID'];
$res = sqlsrv_query($conn, $sql);
if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
$r = sqlsrv_fetch_array($res, 2);
if (!$r) die(json_encode(['status' => false, 'message' => 'Invalid ID']));

if($r['DutyID'] !== NULL)
{
	$row_id = "DutyID-" . $r['DutyID'];
}
elseif ($r['ProjectID'] !== NULL)
{
	$row_id = "ProjectID-" . $r['ProjectID'];
}
elseif($r['SkillID'] !== NULL)
{
	$row_id = "SkillID-" . $r['SkillID'];
}
elseif($r['KPIID'] !== NULL)
{
	$row_id = "KPIID-" . $r['KPIID'];
}

$sql = "DELETE FROM dbo.JobRequirements WHERE JobRequirementID = " . $_POST['JobRequirementID'];

if (sqlsrv_query($conn, $sql)) {
	die(json_encode(['status' => true, 'message' => "Deleted successfully", 'requirement_row_id' => $row_id]));
}
else {
	die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
}

?>