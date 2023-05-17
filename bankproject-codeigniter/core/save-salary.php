<?php
session_start();
require_once("database.php");
include('helpers.php');
validatePostRequest(['EmployeeID']);

if ($_SESSION['UserTypeName'] != 'administrator') {
	$sql = "SELECT CurrentSupervisorID FROM Employees WHERE EmployeeID = " . $_POST['EmployeeID'];
	$res = query($sql);
	$CurrentSupervisor = sqlsrv_fetch_array($res, 2);
	$CurrentSupervisorID = $CurrentSupervisor ? $CurrentSupervisor['CurrentSupervisorID'] : null;

	if ($CurrentSupervisorID == null || $_SESSION['EmployeeID'] != $CurrentSupervisorID) {
		die(json_encode(['status' => false, 'message' => "Only supervisor can perform this action"]));
	}
}

$columns = [
	'EmployeeID',
	'BasicMonthlySalary',
	'GuaranteedAdditionalWage',
	'SalesCommission',
	'DiscretionallyBonus',
	'ProfitShare',
	'Equity',
	// 'NextReviewDate',
	'Notes',
	'UpdatedDate'
];
$values = [
	$_POST['EmployeeID'],
	$_POST['BasicMonthlySalary'],
	$_POST['GuaranteedAdditionalWage'],
	$_POST['SalesCommission'] ?? 0,
	$_POST['DiscretionallyBonus'] ?? 0,
	$_POST['ProfitShare'] ?? 0,
	$_POST['Equity'] ?? 0,
	// $_POST['NextReviewDate'],
	$_POST['Notes'],
	date('Y-m-d H:i:s')
];
[$insert_sql, $update_sql] = getQueries("EmployeeSalaries", $columns, "EmployeeSalaryID");

$res = query("SELECT TOP(1) EmployeeSalaryID FROM EmployeeSalaries WHERE EmployeeID = " . $_POST['EmployeeID'] . " ORDER BY UpdatedDate DESC");
$EmployeeSalaryID = sqlsrv_fetch_array($res, 2);
$EmployeeSalaryID = $EmployeeSalaryID != null ? $EmployeeSalaryID['EmployeeSalaryID'] : null;

if ($EmployeeSalaryID) {
	$values[] = $EmployeeSalaryID;
	query($update_sql, $values);
} else {
	query($insert_sql, $values);
}

echo json_encode(['status' => true, 'message' => "Salary Updated Successfully"]);