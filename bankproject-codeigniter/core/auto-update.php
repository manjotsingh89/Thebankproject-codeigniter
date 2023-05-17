<?php
include("database.php");

session_start();

//saving new or update employee records

if(isset($_POST['submit'])){
	$response = [];

	$id      = isset($_POST['id'])      ? $_POST['id']      : die("No ID to update");
	$idField = isset($_POST['idfield']) ? $_POST['idfield'] : die("No ID Field to update");
	$table   = isset($_POST['table'])   ? $_POST['table']   : die("No table to update");
	$field   = isset($_POST['field'])   ? $_POST['field']   : die("No field to update");
	$value   = isset($_POST['value'])   ? $_POST['value']   : die("No value to update");

	$sql = "UPDATE ".$table." SET ".$field." = '".$value."' WHERE ".$idField." = ".$id;
	$res = sqlsrv_query($conn, $sql);

	if(!$res) die('Problem with query: ' . $sql);

	echo 200;
}else{
	die('Invalid access');
}
?>