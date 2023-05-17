<?php
session_start();
include('../core/helpers.php');
validatePostRequest([]);
include('../core/database.php');
include("../core/jobs-functions.php");


$COLUMN = $_GET['column'] ?? 'ApplicationID';
$ORDER = $_GET['order'] ?? 'ASC';
$j  = getJobs('%', $COLUMN, $ORDER);
$jobs = [];

while ($job = sqlsrv_fetch_array($j, 2)) {
	$jobs[] = $job;
}

foreach($jobs as $row){ ?>
	<tr data-link="job-details.php?id=<?=$row['JobID']?>">
		<td><?=$row['JobID']?></td>
		<td><?=$row['JobTitleName']?></td>
		<td><?=$row['JobStatusName']?></td>
		<td><?=$row['CreatedDate']?></td>
		<td>
			<span><?=$row['FirstName'] . ' ' . $row['LastName'] ?></span><br>
		</td>
		<td>
			<!-- <a class='btn btn-default confirm-delete' javascript="void();" data-link="core/save-jobs.php?type=Jobs&id=<?=$row['JobID']?>&act=delete"><i class='fa fa-trash'></i></a> -->
			<a class='btn btn-default' javascript="void();" href="job-details.php?id=<?=$row['JobID']?>"><i class='fa fa-angle-right'></i></a>
		</td>
	</tr>
<?php }