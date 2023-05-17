<?php
session_start();
include("./helpers.php");
validatePostRequest(['action']);
include("database.php");
include("applications-employees-functions.php");

if ($_POST['action'] == 'create_interview') {

	//Check if application exists
	validatePostRequest(['ApplicationID']);
	$application_id = $_POST['ApplicationID'];
	$res = query("SELECT TOP (1) * FROM dbo.Applications WHERE ApplicationID = '$application_id'");
	$application = sqlsrv_fetch_array($res, 2);
	if ($application == null) die(json_encode(['status' => false, 'message' => 'Application not found']));
	if ($application['ApplicationStatus'] != 'approved') die(json_encode(['status' => false, 'message' => 'Application not approved']));
	$jobID = $application["JobID"];


	//Check if logged in user is allowed to Interview this application
	$interviewer_id = $_SESSION['EmployeeID'];
	
	if ($_SESSION['UserTypeName'] != 'administrator') {
		$res = query("SELECT InterviewerID FROM ApplicationInterviewers WHERE ApplicationID = " . $application['ApplicationID']);
		$interviewers = [];
		while ($interviewer = sqlsrv_fetch_array($res, 2)) {
			$interviewers[] = $interviewer['InterviewerID'];
		}

		if (!in_array($interviewer_id, $interviewers)) {
			die(json_encode(['status' => false, 'message' => 'You are not allowed to Interview this application.']));
		}
	}

	//Check if interview already exists against this interviewer and application
	$sql = "SELECT TOP 1 * FROM dbo.Interviews WHERE ApplicationID = '$application_id' AND EmployeeID = '$interviewer_id'";
	$res = sqlsrv_query($conn, $sql);
	if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
	$interview = sqlsrv_fetch_array($res, 2);

	if ($interview != NULL) {
		if ($interview['InterviewStatusID'] == 2) {
			die(json_encode(['status' => false, 'message' => 'You\'ve already interviewed this application' ]));
		}

		echo json_encode(['status' => true, 'location' => 'interview.php?iid=' . $interview['InterviewID']]);
		exit;
	}

	//Create Interview
	$sql = "INSERT INTO dbo.Interviews (JobID, EmployeeID, ApplicationID, Created) VALUES ('$jobID', '$interviewer_id', '$application_id', '" . date('Y-m-d h:i:s') . "'); SELECT SCOPE_IDENTITY() AS ID";
	$res = sqlsrv_query($conn, $sql);
	if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
	$interview_id = getLastInsertID($res);

	sqlsrv_query($conn, "INSERT INTO InterviewStatistics (InterviewID, CreatedAt) VALUES ('$interview_id', '" . date('Y-m-d H:i:s') . "');");

	//Get job requirements
	$sql = "SELECT * FROM dbo.JobRequirements WHERE JobID = '$jobID';";
	$res = sqlsrv_query($conn, $sql);
	if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));

	//Insert Job Requirements as Interview Requirements
	while ($JobRequirement = sqlsrv_fetch_array($res, 2))
	{
		foreach ($JobRequirement as $column => $value) {
			$x[$column] = $value == '' ? 'NULL' : "'$value'";
		}

		$JobRequirement = (object) $x;
		$sql = "INSERT INTO dbo.InterviewRequirements (InterviewID, DutyID, ProjectID, SkillID, KPIID, ProjectTarget, ProjectQuarter, ProjectYear, KPITargetNum, TargetNumFreqID)
		VALUES (
			'$interview_id',
			$JobRequirement->DutyID,
			$JobRequirement->ProjectID,
			$JobRequirement->SkillID,
			$JobRequirement->KPIID,
			$JobRequirement->ProjectTarget,
			$JobRequirement->ProjectQuarter,
			$JobRequirement->ProjectYear,
			$JobRequirement->KPITargetNum,
			$JobRequirement->TargetNumFreqID
		)";
		sqlsrv_query($conn, $sql);
	}

	echo json_encode(['status' => true, 'location' => 'interview.php?iid=' . $interview_id]);
} elseif ($_POST['action'] == 'hire') {
	validatePostRequest(['ApplicationID', 'OfferAcceptDate']);
	$application_id = $_POST["ApplicationID"];
	$sql = "SELECT JobID, EmployeeID FROM Applications WHERE ApplicationID = '$application_id' AND InterviewCount >= 2";
	$res = query($sql);
	$application = sqlsrv_fetch_array($res, 2);

	if (!$application) {
		die(json_encode(['status' => false, 'message' => 'Interviews Incompleted']));
	}
	
	$res = query("SELECT JobStatusID FROM Jobs WHERE JobID = " . $application["JobID"]);
	$job = sqlsrv_fetch_array($res, 2);

	if ($job["JobStatusID"] != 2) die(json_encode(['status' => false, 'message' => 'Job is not open']));

	query("UPDATE Applications SET ApplicationStatus = 'completed', OfferAcceptDate = '" . $_POST['OfferAcceptDate'] . "' WHERE ApplicationID = " . $application_id);
	query("UPDATE Jobs SET JobStatusID = 3, FilledByEmpID = '" . $application["EmployeeID"] . "' WHERE JobID = '" . $application["JobID"] . "';");
	query("UPDATE Employees SET EmployeeStatusID = '1' WHERE EmployeeID = '" . $application["EmployeeID"] . "';");
	echo json_encode(["status" => true, "message" => "Employee Hired", "EmployeeID" => $application["EmployeeID"], "JobID" => $application["JobID"]]);
} elseif ($_POST['action'] == 'kiv') {
	validatePostRequest(['ApplicationID']);
	$application_id = $_POST["ApplicationID"];
	$sql = "SELECT JobID, EmployeeID FROM Applications WHERE ApplicationID = '$application_id' AND InterviewCount >= 2";
	$res = query($sql);
	$application = sqlsrv_fetch_array($res, 2);

	if (!$application) {
		die(json_encode(['status' => false, 'message' => 'Interviews Incompleted']));
	}

	$res = query("UPDATE Employees SET EmployeeStatusID = '4' WHERE EmployeeID = '" . $application["EmployeeID"] . "';");
	echo json_encode(["status" => true, "message" => "Employee status updated to Keep In View", "EmployeeID" => $application["EmployeeID"], "JobID" => $application["JobID"]]);
} elseif ($_POST['action'] == 'send offer') {
	validatePostRequest(['ApplicationID']);
	$application_id = $_POST["ApplicationID"];
	$sql = "SELECT JobID, EmployeeID FROM Applications WHERE ApplicationID = '$application_id' AND InterviewCount >= 2";
	$res = query($sql);
	$application = sqlsrv_fetch_array($res, 2);

	if (!$application) {
		die(json_encode(['status' => false, 'message' => 'Interviews Incompleted']));
	}

	$res = query("UPDATE Employees SET EmployeeStatusID = '5' WHERE EmployeeID = '" . $application["EmployeeID"] . "';");
	echo json_encode(["status" => true, "message" => "Offer Sent to Employee", "EmployeeID" => $application["EmployeeID"], "JobID" => $application["JobID"]]);
}
?>