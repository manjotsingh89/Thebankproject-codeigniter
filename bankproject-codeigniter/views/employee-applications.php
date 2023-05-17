<?php
session_start();
include('../core/helpers.php');
validatePostRequest([]);
include('../core/database.php');
include("../core/applications-employees-functions.php");

$EmployeeID = $_GET['EmployeeID'] ?? null;
$COLUMN = $_GET['column'] ?? 'ApplicationID';
$ORDER = $_GET['order'] ?? 'ASC';
if ($EmployeeID === null || $EmployeeID == '') {
	die('Employee ID not provided');
}

$e    = getEmployeeDetails($conn, $EmployeeID);
$applications_res = getEmployeeApplications($EmployeeID, $COLUMN, $ORDER);

$applications = [];
while ($application = sqlsrv_fetch_array($applications_res, 2)) {
	$applications[] = $application;
}

foreach($applications as $application) { 
	$res = query("SELECT InterviewerID FROM ApplicationInterviewers WHERE ApplicationID = " . $application['ApplicationID']);
	$interviewers = [];
	while ($row = sqlsrv_fetch_array($res, 2)) {
		$interviewers[] = $row['InterviewerID'];
	}
?>
<tr data-link="view-application.php?id=<?=$application['ApplicationID']?>">
<td><?=$application['ApplicationID']?></td>
<td><?=$application['FirstName'] . ' ' . $application['LastName']?></td>
<td><?=$application['Email']?></td>
<td><?=$application['TelephoneNumber']?></td>
<td><a href="job-details.php?id=<?=$application['JobID']?>"><?=$application['JobTitleName']?></a></td>
<td><?=ucwords($application['ApplicationStatus'])?></td>
<td><?=$application['InterviewCount']?></td>
<td>
	<?php if(($_SESSION['UserTypeName'] == 'administrator' || in_array($_SESSION['EmployeeID'], $interviewers)) && strtolower($e["Status"]) != 'retired' && $application['ApplicationStatus'] == 'approved') {?>
		<a href="core/set-interview.php" class='btn btn-success btn-sm' id="create-interview" data-id="<?=$application['ApplicationID']?>">Interview Now</a>
		<?php if($application['InterviewCount'] >= 3 && $application["JobStatusID"] == 2) {
			if ($e['Status'] == 'Applicant') {?>
			<a href="core/set-interview.php" data-action="kiv" data-id="<?=$application['ApplicationID']?>" class='btn btn-success btn-sm hiring'>KIV</a>
			<a href="core/set-interview.php" data-action="send offer" data-id="<?=$application['ApplicationID']?>" class='btn btn-success btn-sm hiring'>Send Offer</a>
			<?php } elseif ($e['Status'] == 'Offer Sent' || $e['Status'] == 'KIV') {?>
			<a href="core/set-interview.php" data-action="_hire" data-id="<?=$application['ApplicationID']?>" class='btn btn-success btn-sm hiring'>Hire</a>
		<?php }} ?>
	<?php } elseif($application['ApplicationStatus'] == 'pending approval' && $_SESSION['UserTypeName'] == 'administrator') { ?>
		 <button data-status="approve" data-id="<?=$application['ApplicationID']?>" class='btn btn-success btn-sm app-status'>Approve</button>
		 <a href="core/update-application-status.php?ApplicationID=<?=$application['ApplicationID']?>" class='btn btn-danger btn-sm'>Reject</a>
	<?php } ?>
	 <a href="view-application.php?id=<?=$application['ApplicationID']?>" class='btn btn-primary btn-sm' target="_blank">View Application</a></td>
</tr>
<?php } ?>