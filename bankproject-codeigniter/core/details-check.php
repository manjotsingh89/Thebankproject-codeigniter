<?php

//this page is never seen by user. Will process info from the index page and redirect to either 
// page 1
// page 2
// or back to the index page (with a flag so that index can issue warning about password being wrong)

session_start();

if(!$_SESSION['EmployeeID']){
	die("No employee ID");
}

// echo json_encode($_SESSION['EmployeeID']);
header("location: ../employee-details.php?id=".$_SESSION['EmployeeID']);

?>