<?php
include("database.php");
include("jobs-functions.php");
include("applications-employees-functions.php");
session_start();

//saving new or update jobs and requirements records

if(isset($_POST['submit'])){
	$response = [];

	if($_POST['submit'] == 'new-requirement'){

		// insert new requirement through dynamic insert
		$table  = isset($_POST['table']) ? $_POST['table'] : die("No table to update");
		$fields = [];
		$values = [];
		foreach ($_POST as $key => $value) {
			if($key != 'submit' && $key != 'table' && $key != 'type' && $key != 'JobID' && $key != 'TargetNumFreqID' && $key != 'KPITargetNum'){
				$fields[] = $key;
				$values[] = is_numeric($value) ? $value : "'".$value."'";
			}
		}

		// insert new requirement
		$sql    = "INSERT INTO ".$table." (".implode(",", $fields).") VALUES(".implode(",", $values)."); SELECT SCOPE_IDENTITY() AS ID";
		$res    = sqlsrv_query($conn, $sql);

		if(!$res) die('Problem with query: ' . $sql);


		//check if requirement is from jobs then create new job requirement;

		if(isset($_POST['type'])){
		    $lastID = getLastID($res);
			$JobID  = isset($_POST['JobID']) ? $_POST['JobID'] : die("No job id");

			if($_POST['type'] != 'KPIID'){
				$sql = "INSERT INTO dbo.JobRequirements (JobID, ".$_POST['type'].") VALUES(".$JobID.", ".$lastID.")";

			}else{
				$sql = "INSERT INTO dbo.JobRequirements (JobID, ".$_POST['type'].", TargetNumFreqID, KPITargetNum) VALUES(".$JobID.", ".$lastID.", ".$_POST['TargetNumFreqID'].", ".$_POST['KPITargetNum'].")";
			}
			$res = sqlsrv_query($conn, $sql);

			if(!$res) die('Problem with query: ' . $sql);

			header("location: ../job-details.php?id=".$JobID);
		}else{

			header("location: ../job-requirements.php");
		}
	}else{
		die('Unknown request');
	}

}else if(isset($_GET['act']) && $_GET['act'] == "delete"){
	$id    = isset($_GET['id']) ? $_GET['id'] : die("No id");
	$table = isset($_GET['type']) ? $_GET['type'] : die("No type");
	$field = isset($_GET['field']) ? $_GET['field'] : die("No field ");

	$sql = "DELETE FROM ".$table." WHERE ".$field." = ".$id;
	$res = sqlsrv_query($conn, $sql);
	if(!$res) die('Problem with query: ' . $sql);

	header("location: ../job-requirements.php");

}else{
	die('Invalid access');
}
?>