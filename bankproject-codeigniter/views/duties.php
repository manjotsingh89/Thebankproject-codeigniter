<?php
session_start();
include('../core/helpers.php');
validatePostRequest([]);
include('../core/database.php');
include("../core/jobs-functions.php");


$COLUMN = $_GET['column'] ?? 'ApplicationID';
$ORDER = $_GET['order'] ?? 'ASC';

$d  = getDuties(false, $COLUMN, $ORDER);











while($row = sqlsrv_fetch_array($d, SQLSRV_FETCH_ASSOC) ){ ?>
<tr>
	<td><?=$row['DutyID']?></td>
	<td><input type="text" value="<?=$row['DutyName']?>" class="borderless editable" data-id="<?=$row['DutyID']?>" data-table="Duties" data-field="DutyName" data-idfield="DutyID">
	</td>
	<td>
		<select class="borderless editable" value="<?=$row['InLib']?>" class="borderless editable" data-id="<?=$row['DutyID']?>" data-table="Duties" data-field="InLib" data-idfield="DutyID">
			<option value="1" <?=$row['InLib'] == 1 ? 'selected' : ''?>>Yes</option>
			<option value="0" <?=$row['InLib'] == 0 ? 'selected' : ''?>>No</option>
		</select>
	<td>
		<a class='btn btn-default confirm-delete' javascript="void();" data-link="core/save-requirements.php?type=Duties&id=<?=$row['DutyID']?>&act=delete&field=DutyID">
		<i class='fa fa-trash'></i>
		</a>
	</td>
</tr>
<?php } ?>