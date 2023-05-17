<?php 
include("_header.php");

$PassResult = getEmployees($conn);
$statuses_res   = getStatuses($conn);
$statuses = [];
while($row = sqlsrv_fetch_array($statuses_res, SQLSRV_FETCH_ASSOC) ) {
	$statuses[] = $row;
}
?>

<div class="header">
	<div class="row">
		<div class="col-6">
			<h2>Employees</h2>
		</div>
		<div class="col-6">
			<a href="#" class="btn btn-md btn-primary btn-rounded float-end" data-bs-toggle="modal" data-bs-target="#AddEmployeeModal">
				<i class="fas fa-plus"></i> Add New Employee
			</a>
		</div>
	</div>
	<div class="bg-white p-3 my-4 row">
		<div class="col-8">
			<div class="input-group">
				<span class="input-group-text" id="search-icon"><i class="fas fa-search"></i></span>
				<input type="text" class="form-control" placeholder="Search by name or email" id="search-employee-input" aria-label="Recipient's username with two button addons" aria-describedby="search-icon">
			</div>
		</div>
		<div class="d-flex align-items-center col-4">
			<label><b>Filter By Status:</b>&nbsp;&nbsp;&nbsp;</label>
			<select class="form-control" id="status-filter" style="width: auto !important;">
				<option value="all">All</option>
				<?php foreach($statuses as $status){ ?>
					<option value="<?=$status['EmployeeStatusName']?>" <?= (isset($_status) && $_status == $status['EmployeeStatusName']) ? 'selected':''?> >
						<?=$status['EmployeeStatusName']?>
					</option>
				<?php } ?>				
			</select>
		</div>
	
	</div>
	<table class="table table-primary table-hover tr-link" id="table-employees">
		<thead>
			<tr>
				<th class="sortable-e" data-order="DESC" data-column="EmployeeID"><div>Employee No.<i class="arrow arrow-up active"></i></div></th>
				<th class="sortable-e" data-order="ASC" data-column="FirstName"><div>Full Name<i class="arrow arrow-up"></i></div></th>
				<th class="sortable-e" data-order="ASC" data-column="Email"><div>Email<i class="arrow arrow-up"></i></div></th>
				<th class="sortable-e" data-order="ASC" data-column="TelephoneNumber"><div>Telephone Number<i class="arrow arrow-up"></i></div></th>
				<th class="sortable-e" data-order="ASC" data-column="JobTitleName"><div>Current Job<i class="arrow arrow-up"></i></div></th>
				<th class="sortable-e" data-order="ASC" data-column="Status"><div>Status<i class="arrow arrow-up"></i></div></th>
				<th></th>
   		</thead>
		<tbody id="employees-list-container">
			<?php while($row = sqlsrv_fetch_array($PassResult, SQLSRV_FETCH_ASSOC) ){ ?>
			<tr data-link="employee-details.php?id=<?=$row['EmployeeID']?>">
			<td><?=$row['EmployeeID']?></td>
			<td><?=$row['FirstName'].' '.$row['LastName']?></td>
			<td><?=$row['Email']?></td>
			<td><?=$row['TelephoneNumber']?></td>
			<td>
				<?php if ($row['JobID'] != NULL) {?>
				<a href="job-details.php?id=<?=$row['JobID']?>"><?=ucwords($row['JobTitleName'])?></a>
				<?php } ?>
			</td>
			<td><button class='btn btn-<?=getStatusColor($row['Status'])?> btn-sm txt-white btn-sm-rounded'><?=ucwords($row['Status'])?></button></td>
			<td><a href='employee-details.php?id=<?=$row['EmployeeID']?>' class='btn btn-default'><i class='fas fa-angle-right'></i></a></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
	<div class="text-center" id="employees-list-loader" style="display: none;">
		<div id="loading"></div>
	</div>
</div>


<!-- New employee modal -->
<div class="modal fade modal-md" id="AddEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="AddEmployeeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="AddEmployeeModalLabel">Add new employee</h5>
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
			<form method="POST" action="core/save-employee.php" id="add-new-employee-form">
				<div class="modal-body">
					<div class="form-group row mb-2">
						<label for="FirstName" class="col-sm-3 col-form-label">First Name:</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="FirstName" name="FirstName" required="true" placeholder="Enter first name">
						</div>
					</div>
					
					<div class="form-group row mb-2">
						<label for="LastName" class="col-sm-3 col-form-label">Last Name:</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="LastName" name="LastName" required="true" placeholder="Enter last name">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label for="Email" class="col-sm-3 col-form-label">Employee Email:</label>
						<div class="col-sm-9">
							<input type="email" class="form-control" id="Email" name="Email" required="true" placeholder="Enter email">
						</div>
					</div>
					
					<div class="form-group row mb-2">
						<label for="EmployeeStatusID" class="col-sm-3 col-form-label">Status:</label>
						<div class="col-sm-9">
							<select id="EmployeeStatusID" name="EmployeeStatusID" class="form-control" required="true">
							<?php foreach($statuses as $row){ ?>
								<option value="<?=$row['EmployeeStatusID']?>"><?=$row['EmployeeStatusName']?></option>
							<?php } ?>
							</select>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="submit" value="new-employee">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php include("_footer.php"); ?>


