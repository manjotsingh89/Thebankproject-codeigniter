<?php
session_start();
include('./helpers.php');
validatePostRequest(['JobID', 'table']);
include('./database.php');

$table_columns = [
	"Duties" => ["DutyName"],
	"Projects" => ["ProjectName"],
	"KPIs" => ["KPITitle", "KPICategoryID"],
	"Skills" => ["SkillName"],
];

$requirement_column = [
	"Duties" => "DutyID",
	"Projects" => "ProjectID",
	"KPIs" => "KPIID",
	"Skills" => "SkillID",
];

$requirement_display_columns = [
	"Duties" => ["DutyID", "DutyName"],
	"Projects" => ["ProjectID", "ProjectName"],
	"KPIs" => ["KPIID", "KPITitle", "CategoryName"],
	"Skills" => ["SkillID", "SkillName"],
];

$res = sqlsrv_query($conn, "SELECT * FROM Frequencies");
$freq_options = '<option>Select Frequency</option>';
while ($row = sqlsrv_fetch_array($res, 2)) {
	$freq_options .= '<option value="' . $row["FreqID"] . '">' . $row["FreqName"] . '</option>';
}

$job_requirement_display_columns = [
	"Duties" => ["DutyID", "DutyName"],
	"Projects" => ["ProjectID", "ProjectName", ["name" => "ProjectTarget", "tag" => "input", "type" => "number"], ["name" => "ProjectQuarter", "tag" => "input", "type" => "number"], ["name" => "ProjectYear", "tag" => "input", "type" => "number"]],
	"KPIs" => ["KPIID", "KPITitle", "CategoryName", ["name" => "KPITargetNum", "tag" => "input", "type" => "number"], 'per', ["name" => "TargetNumFreqID", "tag" => "select", "options" => $freq_options]],
	"Skills" => ["SkillID", "SkillName"],
];

$table = $_POST["table"];
validatePostRequest($table_columns[$table]);

$table_values = [];
foreach ($table_columns[$table] as $column) {
	$table_values[] = "'$_POST[$column]'";
}

$table_values[] = isset($_POST['InLib']) && $_POST['InLib'] ? 1 : 0;

$sql = "INSERT INTO dbo.$table (" . implode(", ", $table_columns[$table]) . ", InLib) VALUES (" . implode(", ", $table_values) . "); SELECT SCOPE_IDENTITY() AS ID;";
$res = sqlsrv_query($conn, $sql);
if (!$res) die(json_encode(['status' => false, 'message' => sqlsrv_errors()[0]['message']]));

$requirement_id = getLastInsertID($res);
$jobID = $_POST['JobID'];

$job_requirement_id = addJobRequirement($conn, $jobID, $requirement_column[$table], $requirement_id);

$sql = "SELECT TOP 1 * FROM dbo.JobRequirements
	LEFT JOIN dbo.$table ON dbo.$table." . $requirement_column[$table] . " = dbo.JobRequirements." . $requirement_column[$table] . "
	" . ($table == "KPIs" ? " Join dbo.KPICategories ON dbo.KPICategories.KPICategoryID = dbo.KPIs.KPICategoryID" : "") . "
	WHERE JobRequirementID = $job_requirement_id";

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

$requirement = '<tr style="display: none;" id="' . $requirement_display_columns[$table][0] . '-' . $row[$requirement_display_columns[$table][0]] . '" class="align-middle">';
foreach ($requirement_display_columns[$table] as $column) {
	$requirement .= '<td>' . $row[$column] . '</td>';
}

$requirement .= '<td align="center">
<button type="button" class="btn btn-primary add-job-requirement-btn btn-sm" data-column="' . $requirement_display_columns[$table][0] . '" data-value="' . $row[$requirement_display_columns[$table][0]] . '" data-job-id="' . $jobID . '">
	<i class="fa fa-plus"></i> Add to Job
</button>
</td></tr>';

echo json_encode([
	'status' => true,
	'requirement' => $requirement,
	'job_requirement' => $job_requirement,
]);
?>
