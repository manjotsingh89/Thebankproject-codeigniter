<?php
session_start();
include('../helpers.php');
validatePostRequest(['employeeId']);

if ($_SESSION['EmployeeID'] != $_POST['employeeId']) die(json_encode(['status' => false, 'message' => 'Authorization Failed']));

include('../database.php');

$employeeId = $_POST['employeeId'];
$sql 	= "SELECT TOP 1 Employees.*, Jobs.JobID FROM [dbo].[Employees]
			LEFT JOIN Jobs ON Jobs.FilledByEmpID = Employees.EmployeeID
			WHERE EmployeeID = '".(int) $employeeId."'";
$res = sqlsrv_query($conn, $sql);

if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));

$employee = sqlsrv_fetch_array($res, 2);
if ($employee == null) 	die(json_encode(['status' => false, 'message' => 'Employee not found']));
if (dateDifference(date('Y-m-d'), date($employee['NextReviewDate'])) > 10) die(json_encode(['status' => false, 'message' => 'Employee Review is not due']));
if ($employee["CurrentSupervisorID"] == null) 	die(json_encode(['status' => false, 'message' => 'No supervisor found to submit review.']));

$confirmationReview = sqlsrv_fetch_array(sqlsrv_query($conn, "SELECT TOP 1 * FROM [dbo].[Reviews] WHERE RevieweeID = '".(int) $employeeId."' AND ReviewTypeID = '1'"), 2);
$reviewTypeID = $confirmationReview == null ? 1 : 2;
$sql = "INSERT INTO dbo.Reviews (RevieweeID, ReviewerID, RelatedJobID, ReviewTypeID, ReviewStatusID, ReviewCompletionDate, ReviewSubmissionDate) VALUES ('".$employee['EmployeeID']."', '".$employee['CurrentSupervisorID']."', '".$employee['JobID']."', '$reviewTypeID', '1', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "'); SELECT SCOPE_IDENTITY() AS ID;";
$res = sqlsrv_query($conn, $sql);

if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));

$reviewID = getLastInsertID($res);
$sql = "SELECT * FROM dbo.JobRequirements WHERE JobID = '" . $employee['JobID'] . "';";
$res = sqlsrv_query($conn, $sql);

while ($JobRequirement = sqlsrv_fetch_array($res, 2))
{
	foreach ($JobRequirement as $column => $value) {
		$x[$column] = $value == '' ? 'NULL' : "'$value'";
	}

	$JobRequirement = (object) $x;
	$sql = "INSERT INTO dbo.ReviewRequirements1
		(ReviewID, DutyID, ProjectID, SkillID, KPIID, ProjectTarget, ProjectQuarter, ProjectYear, KPITargetNum, TargetNumFreqID)
		VALUES (
			'$reviewID',
			$JobRequirement->DutyID,
			$JobRequirement->ProjectID,
			$JobRequirement->SkillID,
			$JobRequirement->KPIID,
			$JobRequirement->ProjectTarget,
			$JobRequirement->ProjectQuarter,
			$JobRequirement->ProjectYear,
			$JobRequirement->KPITargetNum,
			$JobRequirement->TargetNumFreqID)
		";
	sqlsrv_query($conn, $sql);
}

$date = new DateTime($employee['NextReviewDate']);
$nextReviewDate = $date->modify('+6 month');
sqlsrv_query($conn, "UPDATE dbo.Employees SET NextReviewDate = '" . $nextReviewDate->format('Y-m-d') . "' WHERE EmployeeID = '$employeeId'");

die(json_encode(['status' => true, 'reviewId' => $reviewID]));

?>