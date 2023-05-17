
<?php

//this page is for all the employees and applications db queries

//get all employees list
function getJobs($jobStatusName = '%', $COLUMN = 'JobID', $ORDER = 'ASC'){
	$sql 	= "SELECT * FROM dbo.[JobsList] WHERE JobStatusName LIKE '$jobStatusName' ORDER BY $COLUMN $ORDER";
	$res = query($sql);
	return $res;
}

function getJobTitles($COLUMN = "JobTitleName", $ORDER = "ASC"){
	$sql 	= "SELECT * FROM JobTitles JOIN JobCategories ON JobCategories.JobCategoryID = JobTitles.JobCategoryID ORDER BY $COLUMN $ORDER";
	$res = query($sql);	
	return $res;
}

function getJobCategories($conn){
	$sql 	= "SELECT * FROM JobCategories ORDER BY CategoryName ASC";
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return $res;
}

function getJobStatuses($conn){
	$sql = "SELECT * FROM JobStatuses";
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}

	$job_statuses = [];

	while ($row = sqlsrv_fetch_array($res, 2)) {
		$job_statuses[] = $row;
	}
	
	return $job_statuses;
}

function getJobDetails($conn, $id){
	$sql 	= "SELECT j.JobSales, j.JobID, j.FilledByEmpID, j.JobDescription, j.CreatedDate, j.FilledDate, js.JobStatusName, jt.JobTitleName, e.FirstName, e.LastName FROM dbo.Jobs as j
			INNER JOIN dbo.JobStatuses as js ON j.JobStatusID = js.JobStatusID
			INNER JOIN dbo.JobTitles as jt ON j.JobTitleID = jt.jobTitleID
			LEFT JOIN Employees as e ON j.FilledByEmpID = e.EmployeeID
			WHERE j.JobID = ".$id;
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return sqlsrv_fetch_array($res, 2);
}

// function getJobNotes($conn, $id) {
// 	$sql = "SELECT * FROM JobNotes WHERE JobID = $id";
// 	$res = sqlsrv_query($conn, $sql);

// 	if( $res === false ) {
// 	     die( print_r( sqlsrv_errors(), true));
// 	}
	
// 	return $res;
// }

function getJobApplications($conn, $id){
	$sql 	= "SELECT * FROM Applications WHERE JobID = ".$id." ORDER BY ApplicationID DESC";
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return $res;
}

function getDuties($inLib = false, $COLUMN = "DutyID", $ORDER = "ASC"){
	$where = !$inLib ? '' : 'WHERE InLib = 1';
	$sql 	= "SELECT * FROM Duties ".$where." ORDER BY $COLUMN $ORDER";
	$res = query($sql);	
	return $res;
}

function getProjects($inLib = false, $COLUMN = "ProjectID", $ORDER = "DESC"){
	$where = !$inLib ? '' : 'WHERE InLib = 1';
	$sql 	= "SELECT * FROM Projects ".$where." ORDER BY $COLUMN $ORDER";
	$res = query($sql);
	return $res;
}

function getSkills($inLib = false, $COLUMN = "SkillID", $ORDER = "DESC"){
	$where = !$inLib ? '' : 'WHERE InLib = 1';
	
	$sql 	= "SELECT * FROM Skills ".$where." ORDER BY $COLUMN $ORDER";
	$res = query($sql);
	return $res;
}

function getKPIs($inLib = false, $COLUMN = "KPIID", $ORDER = "DESC"){
	$where = !$inLib ? '' : 'WHERE k.InLib = 1';
	$sql 	= "SELECT k.*, kc.CategoryName FROM KPIs as k INNER JOIN KPICategories as kc ON k.KPICategoryID = kc.KPICategoryID ".$where." ORDER BY $COLUMN $ORDER";
	$res = query($sql);
	return $res;
}


// function getKPIs($conn, $inLib = false){

// 	$where = !$inLib ? '' : 'WHERE k.InLib = 1';

// 	$sql 	= "SELECT k.*, f.FreqName, kc.CategoryName FROM KPIs as k INNER JOIN Frequencies as f ON k.TargetNumFreqID = f.FreqID INNER JOIN KPICategories as kc ON k.KPICategoryID = kc.KPICategoryID ".$where." ORDER BY k.KPIID DESC";
// 	$res = sqlsrv_query($conn, $sql);

// 	if( $res === false ) {
// 	     die( print_r( sqlsrv_errors(), true));
// 	}
	
// 	return $res;
// }

function getKPIFreq($conn){
	$sql 	= "SELECT * FROM Frequencies ORDER BY FreqID ASC";
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return $res;
}

function getKPICategories($conn){
	$sql 	= "SELECT * FROM KPICategories ORDER BY KPICategoryID ASC";
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return $res;
}

function getInterviewRequirements($conn, $interview_id) {
	$sql = "SELECT InterviewRequirementScores.Score, Frequencies.*, KPIs.*, Skills.*, InterviewRequirements.*, Duties.*, Projects.* FROM InterviewRequirements
	LEFT JOIN Duties ON Duties.DutyID = InterviewRequirements.DutyID
	LEFT JOIN Projects ON Projects.ProjectID = InterviewRequirements.ProjectID
	LEFT JOIN Skills ON Skills.SkillID = InterviewRequirements.SkillID
	LEFT JOIN KPIs ON KPIs.KPIID = InterviewRequirements.KPIID 
	LEFT JOIN Frequencies ON Frequencies.FreqID = InterviewRequirements.TargetNumFreqID
	LEFT JOIN KPICategories ON KPICategories.KPICategoryID = KPIs.KPICategoryID
	LEFT JOIN InterviewRequirementScores ON InterviewRequirementScores.InterviewRequirementID = InterviewRequirements.InterviewRequirementID
	WHERE InterviewRequirements.InterviewID = '$interview_id'";
	$res = sqlsrv_query($conn, $sql);
	if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
	return $res;
}

function getJobRequirementsByJobID($conn, $id){
	$sql = "SELECT jr.*, d.DutyName, p.ProjectName, s.SkillName, k.KPITitle, jr.KPITargetNum, kf.FreqName, kc.CategoryName FROM JobRequirements as jr
	 LEFT JOIN Duties as d ON d.DutyID = jr.DutyID
	  LEFT JOIN Projects as p ON p.ProjectID = jr.ProjectID
	   LEFT JOIN Skills as s ON s.SkillID = jr.SkillID
	    LEFT JOIN KPIs as k ON k.KPIID = jr.KPIID 
	     LEFT JOIN Frequencies aS kf ON kf.FreqID = jr.TargetNumFreqID LEFT JOIN KPICategories as kc ON kc.KPICategoryID = k.KPICategoryID WHERE jr.JobID = ".$id." ORDER BY jr.JobRequirementID DESC";
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return $res;
}

function getJobInterviews($conn, $id){
	$sql = "SELECT Interviews.*, Interviewer.FirstName AS InterviewerFirstName, Interviewer.LastName AS InterviewerLastName, Interviewee.FirstName AS IntervieweeFirstName, Interviewee.LastName AS IntervieweeLastName FROM Interviews
	JOIN Employees AS Interviewer ON Interviewer.EmployeeID = Interviews.EmployeeID
	JOIN Applications ON Applications.ApplicationID = Interviews.ApplicationID
	JOIN Employees AS Interviewee ON Interviewee.EmployeeID = Applications.EmployeeID
	WHERE Interviews.JobID = $id";

	// $sql = "SELECT i.*, a.FirstName, a.LastName, e.FirstName as eFirstName, e.LastName as eLastName FROM Interviews as i 
	// INNER JOIN Applications a ON i.ApplicationID = a.ApplicationID 
	// INNER JOIN Employees e ON e.EmployeeID = i.EmployeeID WHERE i.JobID = ".$id." ORDER BY ApplicationID DESC";

	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return $res;
}

function getInterviewDetails($conn, $id){
	$sql = "SELECT Interviews.*,InterviewStatuses.InterviewStatusName, Employees.Salary FROM Interviews
			JOIN Employees ON Employees.EmployeeID = Interviews.EmployeeID
			JOIN InterviewStatuses ON InterviewStatuses.InterviewStatusID = Interviews.InterviewStatusID
			WHERE InterviewID = ".$id;
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return sqlsrv_fetch_array($res, 2);
}

function getInterviewers($conn, $id){
	$sql = "SELECT i.*, e.FirstName, e.LastName, i.TimeSpent, i.CreatedDate FROM EmployeeInterviewers i JOIN Employees e ON i.EmployeeID = e.EmployeeID WHERE i.InterviewID = ".$id;
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}

	return $res;
}

function getJobRequirementScore($conn, $iID, $jrID){
	$sql = "SELECT * FROM JobRequirementScores WHERE InterviewID = ".$iID." AND JobRequirementID = ".$jrID;
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return sqlsrv_fetch_array($res, 2);
}

function getJobNotes($conn, $id){
	$sql = "SELECT * FROM JobNotes WHERE JobID = ".$id;
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return $res;
}
?>