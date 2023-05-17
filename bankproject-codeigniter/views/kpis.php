<?php
session_start();
include('../core/helpers.php');
validatePostRequest([]);
include('../core/database.php');
include("../core/jobs-functions.php");


$COLUMN = $_GET['column'] ?? 'ApplicationID';
$ORDER = $_GET['order'] ?? 'ASC';

$k  = getKPIs(false, $COLUMN, $ORDER);
$kf = getKPIFreq($conn);
$kc = getKPICategories($conn);
$freqs = [];
$cats  = [];
while($row = sqlsrv_fetch_array($kf, SQLSRV_FETCH_ASSOC) ){ $freqs[] = $row;}
while($row = sqlsrv_fetch_array($kc, SQLSRV_FETCH_ASSOC) ){ $cats[] = $row;}



while($row = sqlsrv_fetch_array($k, SQLSRV_FETCH_ASSOC) ){ ?>
<tr>
	<td><?=$row['KPIID']?></td>
	<td><input type="text" value="<?=$row['KPITitle']?>" class="borderless editable" data-id="<?=$row['KPIID']?>" data-table="KPIs" data-field="KPITitle" data-idfield="KPIID">

	</td>
	<td>
		<select class="borderless editable" name="KPICategoryID" data-id="<?=$row['KPIID']?>" data-table="KPIs" data-field="KPICategoryID" data-idfield="KPIID">
	<?php foreach($cats as $cat){ ?>
		<option value="<?=$cat['KPICategoryID']?>" <?=$row['KPICategoryID'] == $cat['KPICategoryID'] ? 'selected' : ''?>><?=$cat['CategoryName']?></option>
	<?php } ?>
	</select>

	</td>
	<td>
		<select class="borderless editable" value="<?=$row['InLib']?>" class="borderless editable" data-id="<?=$row['KPIID']?>" data-table="KPIs" data-field="InLib" data-idfield="KPIID">
			<option value="1" <?=$row['InLib'] == 1 ? 'selected' : ''?>>Yes</option>
			<option value="0" <?=$row['InLib'] == 0 ? 'selected' : ''?>>No</option>
		</select>
	</td>
	<td>
		<a class='btn btn-default confirm-delete' javascript="void();" data-link="core/save-requirements.php?type=KPIs&id=<?=$row['KPIID']?>&act=delete&field=KPIID">
		<i class='fa fa-trash'></i>
		</a>
	</td>
</tr>
<?php } ?>