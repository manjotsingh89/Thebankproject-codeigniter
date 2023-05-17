<?php
session_start();
include('../helpers.php');
include('../database.php');
validatePostRequest(["ReviewID", "ReviewAction", "ReviewType"]);
$reviewID = $_POST['ReviewID'];
$res = query("SELECT TOP 1 * FROM dbo.Reviews WHERE ReviewID = " . $reviewID);
$review = sqlsrv_fetch_array($res, 2);
if (!$review) die(json_encode(['status' => false, 'message' => 'Invalid Review ID']));

if ($_POST["ReviewAction"] == "submit") {
	if ($_SESSION['EmployeeID'] != $review['RevieweeID']) die(json_encode(['status' => false, 'message' => 'Authorization Failed']));
	$data = [];

	if ($_POST['ReviewType'] == "confirmation") {
		$answersTable = "ReviewAnswers1";
		$questions = ["Q1a", "Q1b", "Q1c", "Q1d", "Q1e", "Q1f", "Q1g", "Q1h", "Q2", "Q3a", "Q3b", "Q3c", "Q3d", "Q3e", "Q3f", "Q3g", "Q3h", "Q3i", "Q3j", "Q4a", "Q4b", "Q5a", "Q5b", "Q5c", "Q5d", "Q5e", "Q5f", "Q5g", "Q5h", "Q5i", "Q5j", "Q5k", "Q5l", "Q5m", "Q5n", "Q5o", "Q6a", "Q6b", "Q6c", "Q6d", "Q6e", "Q6f", "Q6g", "Q6h", "Q6i", "Q7a", "Q7b", "Q7c", "Q7d", "Q7e", "Q7f", "Q7g", "Q7h", "Q7i", "Q8a", "Q8b", "Q8c", "Q8d", "Q8e", "Q8f", "Q8g", "Q9a", "Q9b", "Q9c", "Q9d", "Q9e", "Q9f", "Q9g", "Q9h", "Q10a", "Q10b", "Q10c", "Q10d", "Q10e", "Q10f", "Q10g", "Q10h", "Q10i", "Q10j", "Q10k", "Q10l", "Q11a", "Q11b", "Q11c", "Q11d", "Q11e", "Q11f", "Q11g", "Q11h", "Q11i", "Q11j", "Q11k", "Q11l", "Q11m", "Q11n", "Q11o", "Q12", "Q13", "Q20"];
	} else {
		$answersTable = "ReviewAnswers";
		$questions = ["Q1", "Q2", "Q3a", "Q3b", "Q3c", "Q3d", "Q3e", "Q3f", "Q3g", "Q3h", "Q4a", "Q4b", "Q4c", "Q4d", "Q4e", "Q4f", "Q4g", "Q4h", "Q4i", "Q5", "Q6", "Q7"];
	}


	foreach ($questions as $question) {
		$data[$question] = $_POST[$question];
	}

	$sql = "INSERT INTO dbo.$answersTable (ReviewID, " . implode(', ', array_keys($data)) . ") VALUES ('$reviewID', '" . implode("', '", array_values($data)) . "');\r\n";

	foreach ($_POST['requirements'] ?? [] as $requirement) {
		$sql .= "INSERT INTO dbo.ReviewRequirementScores (ReviewID, ReviewRequirement1ID, Score) VALUES ('$reviewID', '$requirement', '" . $_POST['score-' . $requirement] . "');\r\n";
	}

	foreach ($_POST['KPIID'] ?? [] as $index => $kpiID) {
		$kpiTargetNum = $_POST['KPITargetNum'][$index];
		$kpiTargetFreq = $_POST['KPITargetFreq'][$index];
		$sql .= "INSERT INTO dbo.ReviewRequirements (ReviewID, KPIID, KPITargetNum, TargetNumFreqID) VALUES ('$reviewID', '$kpiID', '$kpiTargetNum', '$kpiTargetFreq');\r\n";
	}

	foreach ($_POST['ProjectID'] ?? [] as $index => $projectID) {
		$projectTarget = $_POST['ProjectTarget'][$index];
		$projectQuarter = $_POST['ProjectQuarter'][$index];
		$projectYear = $_POST['ProjectYear'][$index];
		$sql .= "INSERT INTO dbo.ReviewRequirements (ReviewID, ProjectID, ProjectTarget, ProjectQuarter, ProjectYear) VALUES ('$reviewID', '$projectID', '$projectTarget', '$projectQuarter', '$projectYear');\r\n";
	}

	if ($_POST['ReviewType'] == "confirmation") {
		foreach ($_POST['DutyID'] ?? [] as $index => $projectID) {
			$dutyID = $_POST['DutyID'][$index];
			$sql .= "INSERT INTO dbo.ReviewRequirements (ReviewID, DutyID) VALUES ('$reviewID', '$dutyID');\r\n";
		}	

		foreach ($_POST['Benchmark'] ?? [] as $index => $benchmark) {
			$salaryBenchmark = $_POST['Benchmark'][$index];
			$score = $_POST["score-benchmark-$index"];
			$sql .= "INSERT INTO [dbo].[ReviewBenchmarks] (ReviewID, SalaryBenchmark, Score) VALUES ('$reviewID', '$salaryBenchmark', '$score');\r\n";
		}
	}
	
	$res = sqlsrv_query($conn, $sql);
	if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));

	$sql = "UPDATE dbo.Reviews SET ReviewStatusID = 2, ReviewCompletionDate = '" . str_replace("T", " ", $_POST['ReviewCompletionDate']) . "', ReviewSubmissionDate = '" . str_replace("T", " ", $_POST['ReviewSubmissionDate']) . "' WHERE ReviewID = $reviewID;";
	$res = sqlsrv_query($conn, $sql);
	if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));

	echo json_encode(['status' => true, "message" => "Review Submitted Successfully", "EmployeeID" => $_SESSION['EmployeeID']]);
} elseif ($_POST["ReviewAction"] == "complete") {
	if ($_SESSION['EmployeeID'] != $review['ReviewerID']) die(json_encode(['status' => false, 'message' => 'Authorization Failed']));
	
	if ($_POST['ReviewType'] == "confirmation") {
		$questions = ["Q14", "Q15", "Q17", "Q18", "Q19"];
		$answersTable = "ReviewAnswers1";
	} else {
		$questions = ["Q8", "Q9", "Q11"];
		$answersTable = "ReviewAnswers";
	}
	validatePostRequest($questions);

	$salary_columns = [
		'EmployeeID',
		'BasicMonthlySalary',
		'GuaranteedAdditionalWage',
		'SalesCommission',
		'DiscretionallyBonus',
		'ProfitShare',
		'Equity',
		// 'NextReviewDate',
		'ReviewDate',
		'Notes',
		'ReviewID',
		'UpdatedDate'
	];
	$values = [
		$review['RevieweeID'],
		$_POST['BasicMonthlySalary'],
		$_POST['GuaranteedAdditionalWage'],
		$_POST['SalesCommission'] ?? 0,
		$_POST['DiscretionallyBonus'] ?? 0,
		$_POST['ProfitShare'] ?? 0,
		$_POST['Equity'] ?? 0,
		// $_POST['NextReviewDate'],
		date('Y-m-d H:i:s'),
		$_POST['Notes'],
		$review['ReviewID'],
		date('Y-m-d H:i:s')
	];
	[$insert_sql, $update_sql] = getQueries("EmployeeSalaries", $salary_columns, "EmployeeSalaryID");
	query($insert_sql, $values);

	$data = [];

	foreach ($questions as $question) {
		$answer = $_POST[$question];
		$data[] = $question . " = '$answer'";
	}

	$sql = "UPDATE dbo.$answersTable SET " . implode(', ', $data) . " WHERE ReviewID = " . $review['ReviewID'] . ";\r\n";

	foreach ($_POST['KPIID'] ?? [] as $index => $kpiID) {
		$kpiTargetNum = $_POST['KPITargetNum'][$index];
		$kpiTargetFreq = $_POST['KPITargetFreq'][$index];
		$sql .= "INSERT INTO dbo.ReviewRequirements (ReviewID, KPIID, KPITargetNum, TargetNumFreqID) VALUES ('$reviewID', '$kpiID', '$kpiTargetNum', '$kpiTargetFreq');\r\n";
	}

	foreach ($_POST['ProjectID'] ?? [] as $index => $projectID) {
		$projectTarget = $_POST['ProjectTarget'][$index];
		$projectQuarter = $_POST['ProjectQuarter'][$index];
		$projectYear = $_POST['ProjectYear'][$index];
		$sql .= "INSERT INTO dbo.ReviewRequirements (ReviewID, ProjectID, ProjectTarget, ProjectQuarter, ProjectYear) VALUES ('$reviewID', '$projectID', '$projectTarget', '$projectQuarter', '$projectYear');\r\n";
	}

	if ($_POST['ReviewType'] == "confirmation") {
		foreach ($_POST['DutyID'] ?? [] as $index => $projectID) {
			$dutyID = $_POST['DutyID'][$index];
			$sql .= "INSERT INTO dbo.ReviewRequirements (ReviewID, DutyID) VALUES ('$reviewID', '$dutyID');\r\n";
		}
	}

	$res = sqlsrv_query($conn, "DELETE FROM dbo.ReviewRequirements WHERE ReviewID = " . $review["ReviewID"]);
	if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
	
	$res = sqlsrv_query($conn, $sql);
	if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));

	$sql = "UPDATE dbo.Reviews SET ReviewStatusID = 3, ReviewCompletionDate = '" . str_replace("T", " ", $_POST['ReviewCompletionDate']) . "', ReviewSubmissionDate = '" . str_replace("T", " ", $_POST['ReviewSubmissionDate']) . "' WHERE ReviewID = $reviewID;";
	$res = sqlsrv_query($conn, $sql);

	echo json_encode(["status" => true, "message" => "Review Completed Successfully", "EmployeeID" => $review['RevieweeID']]);

} else {
	echo json_encode(['status' => false, 'message' => "Invalid Action"]);
}
?>
