<?php
include("database.php");
include("helpers.php");
include("applications-employees-functions.php");

session_start();

//saving new or update jobs and requirements records

if(isset($_POST['submit'])){
	$response = [];

	if($_POST['submit'] == 'new-job-title'){
		$title    = isset($_POST['JobTitleName']) ? $_POST['JobTitleName'] : die('No Title to save');
		$description    = isset($_POST['JobDescription']) ? $_POST['JobDescription'] : die('No Title to save');
		$category_id    = isset($_POST['JobCategoryID']) ? $_POST['JobCategoryID'] : die('No Title to save');
		// insert new Job title
		$sql = "INSERT INTO dbo.JobTitles (JobTitleName, JobDescription, JobCategoryID) VALUES ('$title', '$description', '$category_id')";
		$res = sqlsrv_query($conn, $sql);

		if(!$res) die('Problem with query: ' . $sql);

		header("location: ../job-titles.php");
	}else if($_POST['submit'] == 'new-job'){
		// insert new job
		validatePostRequest(['JobTitleID']);

		$sql = "SELECT TOP 1 JobDescription FROM dbo.JobTitles WHERE JobTitleID = " . $_POST['JobTitleID'];
		$res = sqlsrv_query($conn, $sql);
		$job_title = sqlsrv_fetch_array($res, 2);
		if($job_title == null) die('Job Title not found');
		$job_description = $job_title['JobDescription'];
		$sql = "INSERT INTO dbo.Jobs (JobTitleID, JobDescription, JobStatusID, CreatedDate, FilledDate) VALUES(".$_POST['JobTitleID'].", '$job_description', 1, '".date('Y-m-d h:i:s')."', '".date('Y-m-d h:i:s')."')";
		$res = sqlsrv_query($conn, $sql);

		if(!$res) die('Problem with query: ' . $sql);

		header("location: ../jobs.php");

	}else if($_POST['submit'] == 'new-kpi-category'){
		$category    = isset($_POST['CategoryName']) ? $_POST['CategoryName'] : die('No Category to save');
		// insert kpi category
		
		$sql = "INSERT INTO dbo.KPICategories (CategoryName) VALUES('".$category."')";
		$res = sqlsrv_query($conn, $sql);

		if(!$res) die('Problem with query: ' . $sql);

		header("location: ../admin-edit.php");

	}else if($_POST['submit'] == 'new-job-category'){
		$category    = isset($_POST['CategoryName']) ? $_POST['CategoryName'] : die('No Category to save');
		// insert kpi category
		
		$sql = "INSERT INTO dbo.JobCategories (CategoryName) VALUES('$category')";
		$res = sqlsrv_query($conn, $sql);

		if(!$res) die('Problem with query: ' . $sql);

		header("location: ../admin-edit.php");

	}else if($_POST['submit'] == 'new-job-requirement'){
		$JobID = isset($_POST['JobID']) ? $_POST['JobID'] : die('No Job id to save');
		$id    = isset($_POST['id'])    ? $_POST['id'] 	  : die('No id to save');
		$type  = isset($_POST['type'])  ? $_POST['type']  : die('No type to save');
		
		// insert new job requirement
		$sql = "INSERT INTO dbo.JobRequirements (JobID, ".$type.") VALUES(".$JobID.", ".$id.")";
		$res = sqlsrv_query($conn, $sql);

		if(!$res) die('Problem with query: ' . $sql);

		echo 200;
	}else{
		die('Unknown request');
	}

}else if(isset($_GET['act']) && $_GET['act'] == "delete"){
	$id   = isset($_GET['id']) ? $_GET['id'] : die("No id");
	$type = isset($_GET['type']) ? $_GET['type'] : die("No type");

	if($type == "JobTitles"){
		$sql = "DELETE FROM JobTitles WHERE JobTitleID = ".$id;
		$res = sqlsrv_query($conn, $sql);
		if(!$res) die('Problem with query: ' . $sql);


		header("location: ../job-titles.php");

	}else if($type == "Jobs"){
		$sql = "DELETE FROM Jobs WHERE JobID = ".$id;
		$res = sqlsrv_query($conn, $sql);
		if(!$res) die('Problem with query: ' . $sql);

		header("location: ../jobs.php");
	}else if($type == "KPICategories"){
		$sql = "DELETE FROM KPICategories WHERE KPICategoryID = ".$id;
		$res = sqlsrv_query($conn, $sql);
		if(!$res) die('Problem with query: ' . $sql);

		header("location: ../admin-edit.php");
	}else if($type == "JobCategories"){
		$sql = "DELETE FROM JobCategories WHERE JobCategoryID = ".$id;
		$res = sqlsrv_query($conn, $sql);
		if(!$res) die('Problem with query: ' . $sql);

		header("location: ../admin-edit.php");
	}
}else if(isset($_GET['act']) && $_GET['act'] == "copy"){
	$id   = isset($_GET['id']) ? $_GET['id'] : die("No id");

	$sql = "INSERT INTO Jobs (JobTitleID, JobStatusID, JobDescription) SELECT JobTitleID, JobStatusID, JobDescription FROM Jobs WHERE JobID = ".$id."; SELECT SCOPE_IDENTITY() AS ID";
	$res = sqlsrv_query($conn, $sql);
	if(!$res) die('Problem with query: ' . $sql);

	$lastID = getLastID($res);

	header("location: ../job-details.php?id=".$lastID);
}else{
	die('Invalid access');
}
?>