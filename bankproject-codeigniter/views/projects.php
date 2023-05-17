<?php
session_start();
include('../core/helpers.php');
validatePostRequest([]);
include('../core/database.php');
include("../core/jobs-functions.php");


$COLUMN = $_GET['column'] ?? 'ApplicationID';
$ORDER = $_GET['order'] ?? 'ASC';

$p  = getProjects(false, $COLUMN, $ORDER);

while($row = sqlsrv_fetch_array($p, SQLSRV_FETCH_ASSOC) ){ ?>
<tr>
<td><?=$row['ProjectID']?></td>
<td><input type="text" value="<?=$row['ProjectName']?>" class="borderless editable" data-id="<?=$row['ProjectID']?>" data-table="Projects" data-field="ProjectName" data-idfield="ProjectID">
</td>
<td>
	<select class="borderless editable" value="<?=$row['InLib']?>" class="borderless editable" data-id="<?=$row['ProjectID']?>" data-table="Projects" data-field="InLib" data-idfield="ProjectID">
		<option value="1" <?=$row['InLib'] == 1 ? 'selected' : ''?>>Yes</option>
		<option value="0" <?=$row['InLib'] == 0 ? 'selected' : ''?>>No</option>
	</select>
</td>
<td>
	<a class='btn btn-default confirm-delete' javascript="void();" data-link="core/save-requirements.php?type=Projects&id=<?=$row['ProjectID']?>&act=delete&field=ProjectID">
	<i class='fa fa-trash'></i>
	</a>
</td>
</tr>
<?php } ?>