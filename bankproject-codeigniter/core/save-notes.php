<?php
include("database.php");
include("jobs-functions.php");
include("applications-employees-functions.php");
session_start();

//saving new or update jobs and requirements records

if(isset($_POST['submit'])){
	$response = [];

	if($_POST['submit'] == 'new-note'){

		// insert new requirement through dynamic insert
		$table  = isset($_POST['table']) ? $_POST['table'] : die("No table to update");
		$fields = [];
		$values = [];
		foreach ($_POST as $key => $value) {
			if($key != 'submit' && $key != 'table'){
				$fields[] = $key;
				$values[] = is_numeric($value) ? $value : "'".$value."'";
			}
		}

		// insert new note
		$sql    = "INSERT INTO ".$table." (".implode(",", $fields).") VALUES(".implode(",", $values).")";
		$res    = sqlsrv_query($conn, $sql);

		if(!$res) die('Problem with query: ' . $sql);

		if ($_POST['table'] == "JobNotes") {
			header("location: ../job-details.php?id=".$_POST['JobID']);
		} else {
			header("location: ../employee-details.php?id=".$_POST['EmployeeID']);
		}

	}else{
		die('Unknown request');
	}

}else{
	die('Invalid access');
}
?>