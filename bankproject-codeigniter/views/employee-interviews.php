<?php
session_start();
include('../core/helpers.php');
validatePostRequest([]);
include('../core/database.php');
include("../core/applications-employees-functions.php");

$EmployeeID = $_GET['EmployeeID'] ?? null;
$COLUMN = $_GET['column'] ?? 'FirstName';
$ORDER = $_GET['order'] ?? 'ASC';
if ($EmployeeID === null || $EmployeeID == '') {
	die('Employee ID not provided');
}

$sql 	= "SELECT a.ApplicationID, t.JobTitleName, j.JobID FROM Applications AS a
			JOIN Jobs j ON a.JobID = j.JobID
			LEFT JOIN JobTitles as t ON j.JobTitleID = t.jobTitleID
		WHERE a.ApplicationStatus != 'draft' AND a.EmployeeID = " . $EmployeeID . ";";
$res = query($sql);
$applications = [];

while ($application = sqlsrv_fetch_array($res, 2)) {
	$applications[$application['ApplicationID']] = [$application['JobID'], $application['JobTitleName']];
}

if (count($applications) == 0) {
	exit();
}
$interviews_res = getApplicationsInterviews(array_keys($applications), $COLUMN, $ORDER);

?>


<?php while ($interview = sqlsrv_fetch_array($interviews_res, 2)) {
	$started_at = new DateTime($interview["StartedAt"]);
	$completed_at = new DateTime($interview["CompletedAt"]);
	$interval = $started_at->diff($completed_at);

	if (isset($applications[$interview['ApplicationID']])) {
		?><tr class="text-center"><td colspan="7"><h4><a href="job-details.php?id=<?=$applications[$interview['ApplicationID']][0]?>"><?=$applications[$interview['ApplicationID']][1]?></a></h4></td></tr><?php
		unset($applications[$interview['ApplicationID']]);
	}
?>
<tr data-link="view-interview.php?id=<?=$interview["InterviewID"]?>">
	<td><?=$interview['FirstName']?> <?=$interview['LastName']?></td>
	<td><?=date("d-M-Y h:i a", strtotime($interview['CreatedAt']))?></td>
	<td><?=date("d-M-Y h:i a", strtotime($interview['StartedAt']))?></td>
	<td><?=$interval->format('%H Hours, %I Minutes') ?></td>
	<td><?=ucwords($interview['ImpressionToHire'])?></td>
	<td><?=$interview["InterviewStatusName"]?></td>
	<td>
		<?php if (strtolower($interview["InterviewStatusName"]) == 'draft' && $interview['EmployeeID'] == $_SESSION['EmployeeID']) { ?>
		<a href="interview.php?iid=<?=$interview["InterviewID"]?>" class='btn btn-primary btn-sm'>Complete Interview</a>
		<?php } else { ?>
		<a href="view-interview.php?id=<?=$interview["InterviewID"]?>" class='btn btn-warning btn-sm'>View Interview</a>
		<?php } ?>

	</td>
</tr>
<?php } ?>