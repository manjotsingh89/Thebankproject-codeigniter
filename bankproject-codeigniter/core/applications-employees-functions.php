<?php

//this page is for all the employees and applications db queries

//get all employees list
function getEmployees($conn, $status = '%'){
	$sql 	= "SELECT * FROM dbo.[EmployeesList] WHERE Status LIKE '$status' ORDER BY EmployeeID DESC";
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return $res;
}

function getEmployeeJob($conn, $id) {
	$res = sqlsrv_query($conn, "SELECT * FROM JobsList WHERE FilledByEmpID = '$id'");
	if (!$res) {die( print_r( sqlsrv_errors(), true));}

	return sqlsrv_fetch_array($res, 2);
}

//get all employees list
function getEmployeeApplications($id, $COLUMN, $ORDER){
	$sql 	= "SELECT a.*, j.JobDescription, j.JobStatusID, t.JobTitleName FROM Applications a
				JOIN Jobs j ON a.JobID = j.JobID
				LEFT JOIN JobTitles as t ON j.JobTitleID = t.jobTitleID WHERE (a.ApplicationStatus != 'draft') AND a.EmployeeID = " . $id . " ORDER BY $COLUMN $ORDER";
	$res = query($sql);
	
	return $res;
}

function getPrimaryApplication($conn, $id){
	$sql 	= "SELECT TOP 1 * FROM Applications
				WHERE PrimaryApplication = 1 AND EmployeeID = " . $id;
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return sqlsrv_fetch_array($res, 2);
}

function getApplicationInterviews($conn, $id){
	$sql = "SELECT Interviews.*, InterviewStatuses.InterviewStatusName, InterviewStatistics.*, Employees.FirstName, Employees.LastName FROM Interviews
		LEFT JOIN InterviewStatistics ON InterviewStatistics.InterviewID = Interviews.InterviewID
		LEFT JOIN InterviewStatuses ON InterviewStatuses.InterviewStatusID = Interviews.InterviewStatusID
		LEFT JOIN Employees ON Employees.EmployeeID = Interviews.EmployeeID
		WHERE Interviews.ApplicationID = $id";

	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return $res;
}

function getApplicationsInterviews(array $applications_ids, $COLUMN, $ORDER){
	$sql = "SELECT Interviews.*, InterviewStatuses.InterviewStatusName, InterviewStatistics.*, Employees.FirstName, Employees.LastName FROM Interviews
		LEFT JOIN InterviewStatistics ON InterviewStatistics.InterviewID = Interviews.InterviewID
		LEFT JOIN InterviewStatuses ON InterviewStatuses.InterviewStatusID = Interviews.InterviewStatusID
		LEFT JOIN Employees ON Employees.EmployeeID = Interviews.EmployeeID
		WHERE Interviews.ApplicationID IN (" . implode(', ', $applications_ids) . ") ORDER BY Interviews.ApplicationID, $COLUMN $ORDER";

	$res = query($sql);	
	return $res;
}


function getStatuses($conn){
	$sql = "SELECT * FROM dbo.EmployeeStatuses";
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}

	return $res;
}

function getLastID($queryID) {
     sqlsrv_next_result($queryID);
     sqlsrv_fetch($queryID);

     return sqlsrv_get_field($queryID, 0);
}

function getEmployeeDetails($conn, $id){
	if (!$id) {
		return false;
	}
	$sql = "SELECT * FROM EmployeesList WHERE EmployeeID = ".$id;
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return sqlsrv_fetch_array($res, 2);
}

function getApplicationDetails($conn, $id){
	$sql = "SELECT Applications.*, Employees.Salary FROM Applications
		JOIN Employees ON Employees.EmployeeID = Applications.EmployeeID
		WHERE Applications.ApplicationID = ".$id;
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return sqlsrv_fetch_array($res, 2);
}

function getEducationalDetailsLevel($conn){
	$sql = "SELECT * FROM EducationalDetailsLevelTypes";
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return $res;

}

function getApplicationEducationalDetails($conn, $id){
	$sql = "SELECT ApplicationEducationalDetails.*, LevelName FROM ApplicationEducationalDetails
		INNER JOIN EducationalDetailsLevelTypes ON ApplicationEducationalDetails.EducationalDetailsLevelID = EducationalDetailsLevelTypes.EducationalDetailsLevelID
		WHERE ApplicationID = ".$id;
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return $res;

}

function getApplicationNationalServices($conn, $id){
	$sql = "SELECT * FROM ApplicationNationalServices WHERE ApplicationID = ".$id;
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return sqlsrv_fetch_array($res, 2);

}

function getApplicationEmploymentHistory($conn, $id){
	$sql = "SELECT * FROM ApplicationEmploymentHistory WHERE ApplicationID = ".$id;
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return $res;

}

function getApplicationReferences($conn, $id){
	$sql = "SELECT * FROM ApplicationReferences WHERE ApplicationID = ".$id;
	$res = sqlsrv_query($conn, $sql);

	if( $res === false ) {
	     die( print_r( sqlsrv_errors(), true));
	}
	
	return $res;

}

function getEmployeeNotes($id, $COLUMN, $ORDER){
	$sql = "SELECT * FROM EmployeeNotes WHERE EmployeeID = $id ORDER BY $COLUMN $ORDER";
	$res = query($sql);	
	return $res;
}

function getEmployeeReviews($id, $COLUMN, $ORDER) {
	$sql = "SELECT Reviews.*, ReviewStatuses.*, ReviewTypes.* ,Employees.FirstName, Employees.LastName FROM Reviews
	JOIN ReviewTypes ON ReviewTypes.ReviewTypeID = Reviews.ReviewTypeID
	JOIN ReviewStatuses ON ReviewStatuses.ReviewStatusID = Reviews.ReviewStatusID
	LEFT JOIN Employees ON Employees.EmployeeID = Reviews.RevieweeID
	WHERE RevieweeID = $id OR ReviewerID = $id ORDER BY $COLUMN $ORDER";
	$res = query($sql);

	while ($row = sqlsrv_fetch_array($res, 2)) {
		if ($row['RevieweeID'] == $id) {
			$arr['my_reviews'][] = $row;
		} else {
			$arr['reviews_submitted_to_me'][] = $row;
		}
	}
	
	return $arr ?? [];
}

function getEmployeeSalary($EmployeeID){
	$sql = "SELECT TOP (1) * FROM EmployeeSalaries WHERE EmployeeID = $EmployeeID ORDER BY UpdatedDate DESC";
	$res = query($sql);
	return sqlsrv_fetch_array($res, 2);
}

function getApplications($COLUMN, $ORDER){
	$sql = "SELECT a.*, t.JobTitleName, EmployeesList.Status AS EmployeeStatusName, j.JobStatusID FROM Applications as a
		JOIN Jobs j ON a.JobID = j.JobID
		LEFT JOIN EmployeesList ON EmployeesList.EmployeeID = a.EmployeeID
		LEFT JOIN JobTitles as t ON j.JobTitleID = t.jobTitleID
		WHERE a.ApplicationStatus != 'draft' ORDER BY $COLUMN $ORDER";
	$res = query($sql);
	$applications = [];

	while ($application = sqlsrv_fetch_array($res, 2)) {
		unset($application['SSMA_TimeStamp']);
		$applications[] = $application;
	}

	return $applications;
}
?>