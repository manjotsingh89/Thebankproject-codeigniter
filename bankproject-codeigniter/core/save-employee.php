<?php
include("database.php");
include("helpers.php");
include("applications-employees-functions.php");

session_start();

//saving new or update employee records

if(isset($_POST['submit'])){
	$response = [];
	//insert new employee if new, else update employee record
	if ($_POST['submit'] == 'new-employee') {
		validatePostRequest(['FirstName', 'LastName', 'Email', 'EmployeeStatusID'], false);
		$sql = "INSERT INTO dbo.Employees (FirstName, LastName, Email, EmployeeStatusID, UserTypeID) VALUES ('" . $_POST['FirstName'] . "', '" . $_POST['LastName'] . "', '" . $_POST['Email'] . "', '" . $_POST['EmployeeStatusID'] . "', 3);
				SELECT SCOPE_IDENTITY() AS ID";
		$res = sqlsrv_query($conn, $sql);
		if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message'] . $sql]));
		$employee_id = getLastInsertID($res);
		$sql = "INSERT INTO dbo.Applications (FirstName, LastName, Email, EmployeeID, PrimaryApplication) VALUES ('" . $_POST['FirstName'] . "', '" . $_POST['LastName'] . "', '" . $_POST['Email'] . "', '$employee_id', 1)";
		$res = sqlsrv_query($conn, $sql);
		if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message'] . $sql]));

		$sql = "SELECT TOP 1 EmployeeID, FirstName, LastName, TelephoneNumber, Email, JobTitleName, Status, JobID FROM dbo.[EmployeesList]
				WHERE EmployeeID = $employee_id";
		$res = query($sql);
		$employee = sqlsrv_fetch_array($res, 2);
		$row['TelephoneNumber'] = $row['TelephoneNumber'] == null ? '' : $row['TelephoneNumber'];
		$employee['JobTitleName'] = ucwords($employee['JobTitleName']);
		$employee['Status'] = ucwords($employee['Status']);
		$employee['StatusColor'] = getStatusColor($employee['Status']);

		die(json_encode(['status' => true, 'message' => "Employee created successfully", 'employee' => $employee]));

	} elseif ($_POST['submit'] == 'new'){
		validatePostRequest(['Email', 'FirstName', 'LastName', 'JobID'], false);
		$job_id = $_POST['JobID'];
		$employee = ["Email", "Suffix", "FirstName", "LastName", "TelephoneNumber", "PassportNUmber", "Citizenship"];

		foreach ($_POST as $key => $value)
		{
			if ($key != 'table' && $key != 'submit' && $key != 'eID')
			{
				$fields[] = $key; $values[] = is_numeric($value) ? $value : "'$value'";

				if (in_array($key, $employee))
				{
					$empFields[] = $key; $empValues[] = is_numeric($value) ? $value : "'".$value."'";
				}
			}
		}



		$empFields[] = 'Password'; $empValues[] = "'".md5('qwerty123')."'";
		$empFields[] = 'EmployeeStatusID'; $empValues[] = 2;
		$empFields[] = 'UserTypeID'; $empValues[] = 3;

		$sql = "SELECT EmployeeID FROM Employees WHERE Email = '" . $_POST['Email'] . "'";
		$res = sqlsrv_query($conn, $sql);
		if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message'] . $sql]));
		$employee = sqlsrv_fetch_array($res, 2);

		if ($employee)
		{
			$employee_id = $employee['EmployeeID'];
			$sql = "SELECT ApplicationID, ApplicationStatus FROM Applications WHERE EmployeeID = '$employee_id' AND JobID = '$job_id'";
			$res = sqlsrv_query($conn, $sql);
			if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message'] . $sql]));
			$application = sqlsrv_fetch_array($res, 2);

			if ($application)
			{
				if ($application['ApplicationStatus'] != 'draft') {
					die(json_encode(['status' => false, 'message' => 'You have already applied for this job.']));
				}

				$sql = "DELETE FROM Applications WHERE ApplicationID = " . $application['ApplicationID'];
				$res = sqlsrv_query($conn, $sql);
				if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message'] . $sql]));
			}
		}
		else
		{
			$sql = "INSERT INTO dbo.Employees (" . implode(",", $empFields) . ") VALUES(" . implode(",", $empValues) . "); SELECT SCOPE_IDENTITY() AS ID";
			$res = sqlsrv_query($conn, $sql);
			if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message'] . $sql]));
			$employee_id = getLastInsertID($res);
		}

		$sql = "SELECT ApplicationID FROM Applications WHERE EmployeeID = '$employee_id' AND PrimaryApplication = 1";
		$res = sqlsrv_query($conn, $sql);
		if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message'] . $sql]));
		$application = sqlsrv_fetch_array($res, 2);
		if (!$application) {
			$fields[] = 'PrimaryApplication';
			$values[] = 1;
		}

		$sql = "INSERT INTO dbo.Applications (".implode(",", $fields).") VALUES(".implode(",", $values)."); SELECT SCOPE_IDENTITY() AS ID";
		$res = sqlsrv_query($conn, $sql);
		if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message'] . $sql]));
		$application_id = getLastInsertID($res);

		$res = sqlsrv_query($conn, "UPDATE Applications SET EmployeeID = '$employee_id' WHERE ApplicationID = '$application_id'");
		if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message'] . $sql]));

		$_SESSION['ApplicantID'] = $employee_id;
		$_SESSION['ApplicationID'] = $application_id;
		die(json_encode(['status' => true, 'application_id' => $_SESSION['ApplicationID'], 'EmployeeID' => $_SESSION['ApplicantID']]));
	}else if($_POST['submit'] == 'update'){

		$appID = isset($_SESSION['ApplicationID']) ? $_SESSION['ApplicationID'] : null;
		$eID   = isset($_SESSION['ApplicantID']) ? $_SESSION['ApplicantID'] : null;

		if ($appID == null) {
			die(json_encode(['status' => false, 'message' => 'Application does not exist.']));
		}

		if($_POST['table'] == 'Applications')
		{
			$fields = "";

			//get all form object
			foreach($_POST as $key => $value){
				if($key != 'table' && $key != 'submit' && $key != 'eID'){
					$value = is_numeric($value) ? $value : "'".$value."'";
					$fields .= $key . ' = ' . $value . ', ';
				}
			}

			//update employees table
			$sql = "UPDATE ".$_POST['table']." SET ".rtrim($fields,', ')." WHERE ApplicationID = " . $appID;
			$res = sqlsrv_query($conn, $sql);
			if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
		}
		elseif($_POST['table'] == 'Employees'){
			$fields = "";
			$eID = $_POST['EmployeeID'];
			//get all form object
			$_POST['Salary'] 	  = isset($_POST['Salary']) 	 ? 1 : 0;
			$_POST['Interviewer'] = isset($_POST['Interviewer']) ? 1 : 0;

			foreach($_POST as $key => $value){
				if($key != 'table' && $key != 'submit' && $key != 'EmployeeID'){
					$value = is_numeric($value) ? $value : "'".$value."'";
					$fields .= $key.'='.$value.', ';
				}
			} 

			//update employees table
			$sql = "UPDATE ".$_POST['table']." SET ".rtrim($fields,', ')." WHERE EmployeeID = ".$eID;
			$res = sqlsrv_query($conn, $sql);

			if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
			die(json_encode(['status' => true, 'message' => 'Employee Updated']));
		}
		elseif($_POST['table'] == 'ApplicationNationalServices')
		{
			$sql1 = "DELETE FROM ApplicationNationalServices WHERE ApplicationID = ".$appID;
			$res1 = sqlsrv_query($conn, $sql1);

			foreach ($_POST as $key => $value) {
				if($key != 'table' && $key != 'submit' && $key != 'eID'){
					$fields[] = $key;
					$values[] = is_numeric($value) ? $value : "'".$value."'";
				}
			}

			$fields[] = 'ApplicationID';
			$values[] = $appID;

			$sql2 = "INSERT INTO dbo.ApplicationNationalServices (".implode(",", $fields).") VALUES(".implode(",", $values).")";
			$res2 = sqlsrv_query($conn, $sql2);

			if (!$res2) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
		}
		elseif ($_POST['table'] == 'ApplicationEducationalDetails')
		{
			$sql1 = "DELETE FROM ApplicationEducationalDetails WHERE ApplicationID = ".$appID;
			$res1 = sqlsrv_query($conn, $sql1);

			if (!$res1) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));


			for ($x = 0; $x < count($_POST['EducationalDetailsLevelID']); $x++){
				//insert educational details table;
				$isGrad = isset($_POST['IsGraduated_'.$x]) ? $_POST['IsGraduated_'.$x] : "";
				$AttendedFrom = $_POST['AttendedFrom_'.$x];
				$AttendedTo = $_POST['AttendedTo_'.$x];
				$fields = "(SchoolName, SchoolCity, SchoolCountry, AttendedFrom, AttendedTo, IsGraduated, SchoolDetails, EducationalDetailsLevelID, ApplicationID)";
				$values = "('".$_POST['SchoolName'][$x]."', '".$_POST['SchoolCity'][$x]."', '".$_POST['SchoolCountry'][$x]."', '$AttendedFrom-01', '$AttendedTo-01', '".$isGrad."', '".$_POST['SchoolDetails'][$x]."', '".$_POST['EducationalDetailsLevelID'][$x]."', ".$appID.")";

				$sql = "INSERT INTO ApplicationEducationalDetails ".$fields." VALUES ".$values;
				$res = sqlsrv_query($conn, $sql);

				if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));

			}
		}
		elseif ($_POST['table'] == 'ApplicationEmploymentHistory')
		{

			$sql1 = "DELETE FROM ApplicationEmploymentHistory WHERE ApplicationID = ".$appID;
			$res1 = sqlsrv_query($conn, $sql1);

			if (!$res1) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));


			for($x = 0; $x < count($_POST['EmployerName']); $x++){
				$EmploymentFrom = $_POST['EmploymentFrom_'.$x];
				$EmploymentTo = $_POST['EmploymentTo_'.$x];
				//insert educational details table;
				$fields = "(EmployerName, EmployerCity, EmployerCountry, Position, EmploymentFrom, EmploymentTo, Salary, ReasonForLeaving, ApplicationID)";
				$values = "('".$_POST['EmployerName'][$x]."', '".$_POST['EmployerCity'][$x]."', '".$_POST['EmployerCountry'][$x]."', '".$_POST['Position'][$x]."', '$EmploymentFrom-01', '$EmploymentTo-01', '".$_POST['Salary'][$x]."', '".$_POST['ReasonForLeaving'][$x]."', ".$appID.")";

				$sql = "INSERT INTO ApplicationEmploymentHistory ".$fields." VALUES ".$values;
				$res = sqlsrv_query($conn, $sql);

				if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));

			}
		}
		elseif($_POST['table'] == 'ApplicationReferences')
		{ 
			$sql1 = "DELETE FROM ApplicationReferences WHERE ApplicationID = ".$appID;
			$res1 = sqlsrv_query($conn, $sql1);

			if(!$res1) die('Problem with query: ' . $sql1);

			for($x = 0; $x < count($_POST['ReferenceName']); $x++){
				//insert educational details table;
				$fields = "(ReferenceName, ReferenceEmail, ReferenceMobile, Association, YearsKnown, ApplicationID)";
				$values = "('".$_POST['ReferenceName'][$x]."', '".$_POST['ReferenceEmail'][$x]."', '".$_POST['ReferenceMobile'][$x]."', '".$_POST['Association'][$x]."', '".$_POST['YearsKnown'][$x]."', ".$appID.")";

				$sql = "INSERT INTO ApplicationReferences ".$fields." VALUES ".$values;
				$res = sqlsrv_query($conn, $sql);

				if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
			}
		}

		die(json_encode(['status' => true, 'message' => 'Updated successfully', 'application_id' => $appID, 'employee_id' => $eID]));
	} else {
		die(json_encode(['status' => false, 'message' => 'Unknown request']));
	}
} else {
	die(json_encode(['status' => false, 'message' => 'Invalid access']));
}
?>