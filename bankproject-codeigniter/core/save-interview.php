<?php
include("database.php");

session_start();

if(isset($_POST['submit'])){
	$response = [];

	$iID   = isset($_POST['iID']) ? $_POST['iID'] : die("No interview id to update");
	
	//update interview record
	if($_POST['table'] == 'InterviewRequirementScores'){

		$fields = [];
		$values = [];
				//get all form object
		foreach($_POST as $key => $value){
			if($key != 'submit' && $key != 'iID' && $key != 'table' && $key != 'position' && $key != 'StartedAt'){
				$valArr  = explode("-", $key);
				$interview_requirement_id    = $valArr[1]; 

				//update job requirements scores table
				$sql = "UPDATE InterviewRequirementScores SET Score = '$value' WHERE InterviewRequirementID = " . $interview_requirement_id;
				$res = sqlsrv_query($conn, $sql);

				if(!$res) die('Problem with query: ' . $sql);

				$values = [$interview_requirement_id, $value];
				//insert if not exists
				if(sqlsrv_rows_affected($res) === 0){
					$sql2 = "INSERT INTO InterviewRequirementScores (InterviewRequirementID, Score) VALUES(?, ?)";
					$res2 = sqlsrv_query($conn, $sql2, $values);

					if(!$res2) die('Problem with query: ' . $sql2);
				}
			}
		}

		echo 200;

	}else if($_POST['table'] == 'Interviews'){
		
		$fields = "";
		$values = [];
		//get all form object
		if ((int) $_POST['position'] == 1) {
			$interview_id = $_POST["iID"];
			query("UPDATE InterviewStatistics SET StartedAt = '" . $_POST["StartedAt"] . "' WHERE InterviewID = $interview_id");
		}
		foreach($_POST as $key => $value){
			if($key != 'table' && $key != 'submit' && $key != 'iID' && $key != 'position' && $key != 'StartedAt'){
				$values[] = $value;
				$fields  .= $key.' = ? , ';
			}
		}

		if ($fields != "") {
			//update interviews table
			$sql = "UPDATE Interviews SET " . rtrim($fields,', ') . " WHERE InterviewID = " . $iID;
			$res = sqlsrv_query($conn, $sql, $values);
			if(!$res) die('Problem with query: ' . $sql);
		}

		if ((int) $_POST['position'] == 14) {
			sqlsrv_query($conn, "UPDATE Interviews SET InterviewStatusID = 2 WHERE InterviewID = $iID");
			$interview = sqlsrv_fetch_array(sqlsrv_query($conn, "SELECT ApplicationID FROM Interviews WHERE InterviewID = $iID"), 2);
			$sql = "UPDATE dbo.Applications SET InterviewCount = InterviewCount + 1 WHERE ApplicationID = '" . $interview["ApplicationID"] . "'";
			sqlsrv_query($conn, $sql);
			sqlsrv_query($conn, "UPDATE InterviewStatistics SET CompletedAt = '" . date('Y-m-d H:i:s') . "' WHERE InterviewID = $iID");
		}


		echo 200;

	}else{
		die('Unknown request');
	}

}else{
	die('Invalid access');
}
?>