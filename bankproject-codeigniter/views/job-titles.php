<?php
session_start();
include('../core/helpers.php');
validatePostRequest([]);
include('../core/database.php');
include("../core/jobs-functions.php");


$COLUMN = $_GET['column'] ?? 'ApplicationID';
$ORDER = $_GET['order'] ?? 'ASC';
$jt = getJobTitles($COLUMN, $ORDER);
$jc = getJobCategories($conn);

$job_categories = [];
while($row = sqlsrv_fetch_array($jc, SQLSRV_FETCH_ASSOC) ){
	$job_categories[] = $row;
}
while($row = sqlsrv_fetch_array($jt, SQLSRV_FETCH_ASSOC) ){ ?>
	<tr data-link="">
		<td><input type="text" value="<?=$row['JobTitleName']?>" class="borderless editable" data-id="<?=$row['jobTitleID']?>" data-table="JobTitles" data-field="JobTitleName" data-idfield="jobTitleID"></td>
		<td><input type="text" value="<?=$row['JobDescription']?>" class="borderless editable" data-id="<?=$row['jobTitleID']?>" data-table="JobTitles" data-field="JobDescription" data-idfield="jobTitleID"></td>
		<td>
			<select class="borderless editable" data-id="<?=$row['jobTitleID']?>" data-table="JobTitles" data-field="JobCategoryID" data-idfield="jobTitleID">
				<?php foreach ($job_categories as $category) {?>
				<option value="<?= $category['JobCategoryID'] ?>" <?= $category['JobCategoryID'] == $row['JobCategoryID'] ? "selected" : "" ?>><?=$category['CategoryName']?></option>
				<?php } ?>
			</select>
		</td>
		<td>
			<a class='btn btn-default confirm-delete' javascript="void();" data-link="core/save-jobs.php?type=JobTitles&id=<?=$row['jobTitleID']?>&act=delete"><i class='fa fa-trash'></i></a>
		</td>
	</tr>
<?php }