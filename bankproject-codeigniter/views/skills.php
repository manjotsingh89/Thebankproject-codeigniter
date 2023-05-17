<?php
session_start();
include('../core/helpers.php');
validatePostRequest([]);
include('../core/database.php');
include("../core/jobs-functions.php");


$COLUMN = $_GET['column'] ?? 'ApplicationID';
$ORDER = $_GET['order'] ?? 'ASC';

$s  = getSkills(false, $COLUMN, $ORDER);




while($row = sqlsrv_fetch_array($s, SQLSRV_FETCH_ASSOC) ){ ?>
<tr>
	<td><?=$row['SkillID']?></td>
	<td><input type="text" value="<?=$row['SkillName']?>" class="borderless editable" data-id="<?=$row['SkillID']?>" data-table="Skills" data-field="SkillName" data-idfield="SkillID">
	</td>
	<td>
		<select class="borderless editable" value="<?=$row['InLib']?>" class="borderless editable" data-id="<?=$row['SkillID']?>" data-table="Skills" data-field="InLib" data-idfield="SkillID">
			<option value="1" <?=$row['InLib'] == 1 ? 'selected' : ''?>>Yes</option>
			<option value="0" <?=$row['InLib'] == 0 ? 'selected' : ''?>>No</option>
		</select>
	</td>
	<td>
		<a class='btn btn-default confirm-delete' javascript="void();" data-link="core/save-requirements.php?type=Skills&id=<?=$row['SkillID']?>&act=delete&field=SkillID">
		<i class='fa fa-trash'></i>
		</a>
	</td>
</tr>
<?php } ?>