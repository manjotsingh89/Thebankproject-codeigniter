<?php include("_header.php");
$jc = getJobCategories($conn);

$job_categories = [];
while($row = sqlsrv_fetch_array($jc, SQLSRV_FETCH_ASSOC) ){
	$job_categories[] = $row;
}



?>

<div class="header">
	<div class="row">
		<div class="col-6">
			<h2>Job Titles</h2>
		</div>
		<div class="col-6">
		</div>
	</div>
	<div class="bg-white p-3 my-4">
		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" id="titles-tab" data-bs-toggle="tab" data-bs-target="#job-panel" type="button" role="tab" aria-controls="job-panel" aria-selected="true">Job Titles</button>
			</li>
<!-- 			<li class="nav-item" role="presentation">
				<button class="nav-link" id="kpi-tab" data-bs-toggle="tab" data-bs-target="#kpi-panel" type="button" role="tab" aria-controls="kpi-panel" aria-selected="false">KPI Categories</button>
			</li> -->
		</ul>

		<div class="tab-content mt-4">
			<div class="tab-pane active" id="job-panel" role="tabpanel" aria-labelledby="titles-tab">
				<div class="row mb-4">
					<div class="col text-right">
						<button class="btn btn-md btn-primary btn-rounded float-end" id="add-job-title" data-bs-toggle="modal" data-bs-target="#JobTitleModal">
							<i class="fas fa-plus"></i> Add new Job Title
						</button>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<table class="table table-primary table-hover tr-link" id="table-jobtitles">
							<thead>
							<tr>
								<th class="sortable" data-order="DESC" data-column="JobTitleName"><div>Job Title<i class="arrow arrow-up active"></i></div></th>
								<th>Description</th>
								<th class="sortable" data-order="ASC" data-column="CategoryName"><div>Category<i class="arrow arrow-up"></i></div></th>
							</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
						<div class="text-center" id="job-titles-loader" style="display: none;">
							<div id="loading"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- New job title modal -->
<div class="modal fade modal-md" id="JobTitleModal" tabindex="-1" role="dialog" aria-labelledby="JobTitleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Add new Job Title</h5>
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
			<form method="POST" action="core/save-jobs.php">
				<div class="modal-body">
					<div class="form-group row mb-2">
						<label for="JobTitleName" class="col-sm-3 col-form-label">Title:</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="JobTitleName" name="JobTitleName" required="true" placeholder="Enter Title">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label for="JobDescription" class="col-sm-3 col-form-label">Description:</label>
						<div class="col-sm-9">
							<textarea class="form-control" id="JobDescription" name="JobDescription" required="true" placeholder="Write description"></textarea>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label for="JobCategoryID" class="col-sm-3 col-form-label">Category:</label>
						<div class="col-sm-9">
							<select name="JobCategoryID" class="form-control">
								<?php foreach ($job_categories as $category) {?>
								<option value="<?= $category['JobCategoryID'] ?>"><?=$category['CategoryName']?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary" value="new-job-title" name="submit">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php include("_footer.php"); ?>
<script type="text/javascript">
	$(document).ready(function(){
		getJobTitles();
	});
</script>
