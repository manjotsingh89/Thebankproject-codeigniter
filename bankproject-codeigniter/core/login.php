<?php

//this page is never seen by user. Will process info from the index page and redirect to either 
// page 1
// page 2
// or back to the index page (with a flag so that index can issue warning about password being wrong)

session_start();

//connect to db
require_once 'database.php';
//check if form was sent 
if(!(isset($_POST['submit']) && $_POST['submit']==1) ) die('Please use <a href = "index.php">login page</a>');

$Email  = isset($_POST['email'])    ? $_POST['email'] : die("No email");
$pwd 	= isset($_POST['password']) ? $_POST['password'] : die("No password");


//check password. If match, log in (create session variables) else set session variables to false. 
$sql = "SELECT Employees.*, UserTypes.UserTypeName FROM Employees
		JOIN UserTypes ON Employees.UserTypeID = UserTypes.UserTypeID
		WHERE Employees.Email='$Email' AND Employees.Password='" . md5($pwd) . "';";

$res = sqlsrv_query($conn, $sql);

if(!$res) die('Problem with query: ' . $sql);

$employee = sqlsrv_fetch_array($res, 2);

if($employee != NULL){
	$_SESSION['EmployeeID']  = $employee["EmployeeID"];
	$_SESSION['Email'] 		 = $employee["Email"];
	$_SESSION['UserType'] 	 = $employee["UserType"];
	$_SESSION['UserTypeName'] 	 = strtolower($employee["UserTypeName"]);
	$_SESSION['FirstName'] 	 = $employee["FirstName"];
	$_SESSION['LastName'] 	 = $employee["LastName"];
	$_SESSION['Salary'] 	 = $employee["Salary"];
	$_SESSION['Interviewer'] = $employee["Interviewer"];
	$_SESSION['Login'] 		 = date("Y-m-d h:i:s a");

	// Redirect 
	header("Location: ../index.php"); 
	exit();
}else{

	// Redirect 
	header("Location: ../login.php?failed=1"); 
	exit();

}


?>