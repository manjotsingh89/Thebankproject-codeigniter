<?php
session_start();
include('./helpers.php');
validatePostRequest(['JobID', 'column', 'value']);
include('./database.php');

$jobID = $_POST['JobID'];
$column = $_POST['column'];
$value = $_POST['value'];

$requirement_table = [
	"DutyID" => "Duties",
	"ProjectID" => "Projects",
	"KPIID" => "KPIs",
	"SkillID" => "Skills",
];

$table = $requirement_table[$column] ?? die(json_encode(['status' => false, 'message' => 'Invalid column']));
$res = sqlsrv_query($conn, "SELECT * FROM Frequencies");
$freq_options = '<option>Select Frequency</option>';
while ($row = sqlsrv_fetch_array($res, 2)) {
	$freq_options .= '<option value="' . $row["FreqID"] . '">' . $row["FreqName"] . '</option>';
}

$sql = "INSERT INTO dbo.JobRequirements (JobID, $column) VALUES ('$jobID', '$value');SELECT SCOPE_IDENTITY() AS ID;";
$res = sqlsrv_query($conn, $sql);
if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));
$job_requirement_id = getLastInsertID($res);

$job_requirement_display_columns = [
	"Duties" => ["DutyID", "DutyName"],
	"Projects" => ["ProjectID", "ProjectName", ["name" => "ProjectTarget", "tag" => "input", "type" => "number"], ["name" => "ProjectQuarter", "tag" => "input", "type" => "number"], ["name" => "ProjectYear", "tag" => "input", "type" => "number"]],
	"KPIs" => ["KPIID", "KPITitle", "CategoryName", ["name" => "KPITargetNum", "tag" => "input", "type" => "number"], 'per', ["name" => "TargetNumFreqID", "tag" => "select", "options" => $freq_options]],
	"Skills" => ["SkillID", "SkillName"],
];


$sql = "SELECT * FROM JobRequirements
	LEFT JOIN Duties ON Duties.DutyID = JobRequirements.DutyID
	LEFT JOIN Projects ON Projects.ProjectID = JobRequirements.ProjectID
	LEFT JOIN Skills ON Skills.SkillID = JobRequirements.SkillID
	LEFT JOIN KPIs ON KPIs.KPIID = JobRequirements.KPIID 
	LEFT JOIN Frequencies ON Frequencies.FreqID = JobRequirements.TargetNumFreqID
	LEFT JOIN KPICategories ON KPICategories.KPICategoryID = KPIs.KPICategoryID
	WHERE JobRequirements.JobRequirementID = '$job_requirement_id'";

$res = sqlsrv_query($conn, $sql);
if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message'], 'sql' => $sql]));
$row = sqlsrv_fetch_array($res, 2);
$row['per'] = 'Per';
$job_requirement = '<tr class="align-middle" id="' . $row["JobRequirementID"] . '"><td class="d-none">' . $row["JobRequirementID"] . '</td>';

foreach ($job_requirement_display_columns[$table] as $index => $column) {
	if ($index == 0) {
		continue;
	}

	if (is_array($column)) {
		$job_requirement .= '<td data-column="' . $column["name"] . '">' . 
		($column["tag"] == 'select' ? '<select class="job-requirement-alt form-control" name="' . $column["name"] . '">' . $column["options"] . '</select>' : '<input class="job-requirement-alt edit-field form-control" name="' . $column["name"] . '" type="' . $column["type"] . '">')
		 . '</td>';
	} else {
		$job_requirement .= '<td data-column="' . $column . '">' . $row[$column] . '</td>';
	}
}

$job_requirement .= '<td><a style="float: right;" type="button" class="btn btn-sm btn-danger delete-job-requirement" data-requirement-id="' . $row["JobRequirementID"] . '">Delete</a></td></tr>';

echo json_encode([
	'status' => true,
	'job_requirement' => $job_requirement,
	'table' => $table,
]);
?>