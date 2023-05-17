<?php include("_header.php");

$jt = getJobTitles();
$jc = getJobCategories($conn);
$kc = getKPICategories($conn);

$job_categories = ['Executive', 'Senior Executive', 'Manager', 'Senior Manger', 'Associate Director', 'Director'];

?>

<div class="header">
	<div class="row">
		<div class="col-6">
			<h2>Admin Edit</h2>
		</div>
		<div class="col-6">
		</div>
	</div>
	<div class="bg-white p-3 my-4">
		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories-panel" type="button" role="tab" aria-controls="categories-panel" aria-selected="true">Job Categories</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="kpi-tab" data-bs-toggle="tab" data-bs-target="#kpi-panel" type="button" role="tab" aria-controls="kpi-panel" aria-selected="false">KPI Categories</button>
			</li>
		</ul>

		<div class="tab-content mt-4">
			<div class="tab-pane active" id="categories-panel" role="tabpanel" aria-labelledby="categories-tab">
				<div class="row mb-4">
					<div class="col text-right">
						<button class="btn btn-md btn-primary btn-rounded float-end" id="add-job-category" data-bs-toggle="modal" data-bs-target="#JobCategoryModal">
							<i class="fas fa-plus"></i> Add new Job Category
						</button>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<table class="table table-primary table-hover tr-link" id="table-jobtitles">
							<thead>
							<tr>
								<th class="p-2" colspan="2">Category Name</th>
							</tr>
							</thead>
							<tbody>
							<?php while($row = sqlsrv_fetch_array($jc, SQLSRV_FETCH_ASSOC) ){ ?>
							<tr>
								<td>
									<input type="text" value="<?=$row['CategoryName']?>" class="borderless editable" data-id="<?=$row['JobCategoryID']?>" data-table="JobCategories" data-field="CategoryName" data-idfield="JobCategoryID">
								</td>
								<td>
									<a class='btn btn-default confirm-delete' javascript="void();" data-link="core/save-jobs.php?type=JobCategories&id=<?=$row['JobCategoryID']?>&act=delete"><i class='fa fa-trash'></i></a>
								</td>
							</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="kpi-panel" role="tabpanel" aria-labelledby="kpi-tab">
				<div class="row mb-4">
					<div class="col text-right">
						<button class="btn btn-md btn-primary btn-rounded float-end" id="add-job-title" data-bs-toggle="modal" data-bs-target="#KPICategoryModal">
							<i class="fas fa-plus"></i> Add new KPI Category
						</button>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<table class="table table-primary table-hover tr-link" id="table-jobtitles">
							<thead>
							<tr>
								<th>KPI Category ID</th>
								<th>Category Name</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							<?php while($row = sqlsrv_fetch_array($kc, SQLSRV_FETCH_ASSOC) ){ ?>
							<tr>
								<td><?=$row['KPICategoryID']?></td>
								<td>
									<input type="text" value="<?=$row['CategoryName']?>" class="borderless editable" data-id="<?=$row['KPICategoryID']?>" data-table="KPICategories" data-field="CategoryName" data-idfield="KPICategoryID">
								</td>
								<td>
									<a class='btn btn-default confirm-delete' javascript="void();" data-link="core/save-jobs.php?type=KPICategories&id=<?=$row['KPICategoryID']?>&act=delete"><i class='fa fa-trash'></i></a>
								</td>
							</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- New job categories modal -->
<div class="modal fade modal-md" id="JobCategoryModal" tabindex="-1" role="dialog" aria-labelledby="KPICategoryModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Add new Job Category</h5>
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
			<form method="POST" action="core/save-jobs.php">
				<div class="modal-body">
					<div class="form-group row mb-2">
						<label for="CategoryName" class="col-sm-3 col-form-label">Category Name:</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="CategoryName" name="CategoryName" required="true">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary" value="new-job-category" name="submit">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- New kpi categories modal -->
<div class="modal fade modal-md" id="KPICategoryModal" tabindex="-1" role="dialog" aria-labelledby="KPICategoryModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Add new KPI Category</h5>
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
			<form method="POST" action="core/save-jobs.php">
				<div class="modal-body">
					<div class="form-group row mb-2">
						<label for="CategoryName" class="col-sm-3 col-form-label">Category Name:</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="CategoryName" name="CategoryName" required="true">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary" value="new-kpi-category" name="submit">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php include("_footer.php"); ?>
