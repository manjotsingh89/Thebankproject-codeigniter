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

$notes   = getEmployeeNotes($EmployeeID, $COLUMN, $ORDER);

?>


<?php while($row = sqlsrv_fetch_array($notes, SQLSRV_FETCH_ASSOC) ){ ?>
<tr>
	<td width="100px"><?=$row['EmployeeNoteID'];?></td>
	<td>
		<input type="text" value="<?=$row['Note']?>" class="borderless editable" data-id="<?=$row['EmployeeNoteID']?>" data-table="EmployeeNotes" data-field="Note" data-idfield="EmployeeNoteID">
	</td>
<td><?=$row['CreatedDate'];?></td>
</tr>
<?php } ?>