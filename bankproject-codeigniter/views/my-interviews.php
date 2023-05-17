<?php
session_start();
include('../core/helpers.php');
validatePostRequest([]);
include('../core/database.php');
include("../core/applications-employees-functions.php");


$COLUMN = $_GET['column'] ?? 'ApplicationID';
$ORDER = $_GET['order'] ?? 'ASC';
$applications = getApplications($COLUMN, $ORDER);


foreach($applications as $application) {
	$res = query("SELECT InterviewerID FROM ApplicationInterviewers WHERE ApplicationID = " . $application['ApplicationID']);
	$interviewers = [];
	while ($row = sqlsrv_fetch_array($res, 2)) {
		$interviewers[] = $row['InterviewerID'];
	}

	if (!in_array($_SESSION['EmployeeID'], $interviewers) && $_SESSION['UserTypeName'] != 'administrator') {
		continue;
	}
	?>
<tr data-link="view-application.php?id=<?= $application['ApplicationID']?>" >
	<td><?=$application['ApplicationID']?></td>
	<td><?=$application['FirstName'] . ' ' . $application['LastName']?></td>
	<td><?=$application['Email']?></td>
	<td><?=$application['TelephoneNumber']?></td>
	<td><a href="job-details.php?id=<?=$application['JobID']?>"><?=$application['JobTitleName']?></a></td>
	<td><?=ucwords($application['ApplicationStatus'])?></td>
	<td><?=$application['InterviewCount']?></td>
	<td>
		<div class="btn-group w-100" role="group" aria-label="Basic example">
			<?php if($application['ApplicationStatus'] == 'approved') {?>
				<a href="core/set-interview.php" class='btn btn-light btn-sm' id="create-interview" data-id="<?=$application['ApplicationID']?>">Interview Now</a>
				<?php if($application['InterviewCount'] >= 3 && $application["JobStatusID"] == 2) {
					if ($application['EmployeeStatusName'] == 'Applicant') {?>
					<a href="core/set-interview.php" data-action="kiv" data-id="<?=$application['ApplicationID']?>" class='btn btn-success btn-sm hiring'>KIV</a>
					<a href="core/set-interview.php" data-action="send offer" data-id="<?=$application['ApplicationID']?>" class='btn btn-success btn-sm hiring'>Send Offer</a>
					<?php } elseif ($application['EmployeeStatusName'] == 'Offer Sent' || $application['EmployeeStatusName'] == 'KIV') {?>
					<a href="core/set-interview.php" data-action="_hire" data-id="<?=$application['ApplicationID']?>" class='btn btn-success btn-sm hiring'>Hire</a>
				<?php }} ?>
			<?php } elseif($application['ApplicationStatus'] == 'pending approval' && $_SESSION['UserTypeName'] == 'administrator') { ?>
			 <button data-status="approve" data-id="<?=$application['ApplicationID']?>" class='btn btn-success btn-sm app-status'>Approve</button>
			 <a href="core/update-application-status.php?ApplicationID=<?=$application['ApplicationID']?>" class='btn btn-danger btn-sm'>Reject</a>
			<?php } ?>
			 <a href="view-application.php?id=<?=$application['ApplicationID']?>" class='btn btn-primary btn-sm' target="_blank">View</a>
		</div>
	</td>
</tr>
<?php } ?>