<?php 
include("_header.php"); 

$jt = getJobTitles();
$jobs = [];

?>
<div class="header">
	<div class="row">
		<div class="col-6">
			<h2>Jobs</h2>
		</div>
		<div class="col-6">
			<div class="float-end">
				<button class="btn btn-md btn-primary btn-rounded" data-bs-toggle="modal" data-bs-target="#NewJobModal">
					<i class="fas fa-plus"></i> New Job
				</button>
				<a class="btn btn-md btn-primary btn-rounded" href="public-jobs.php">
					<i class="fas fa-eye"></i> View Open Jobs
				</a>
			</div>
		</div>
	</div>
	<div class="bg-white p-3 my-4">
		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" id="jobs-tab" data-bs-toggle="tab" data-bs-target="#jobs-lists" type="button" role="tab" aria-controls="jobs-lists" aria-selected="true">Jobs List</button>
			</li>
		</ul>

		<div class="tab-content mt-4">
			<div class="tab-pane active" id="jobs-lists" role="tabpanel" aria-labelledby="jobs-tab">
				<div class="row">
					<div class="col-12">
						<table class="table table-primary table-hover tr-link" id="table-jobs">
							<thead>
								<tr>
									<th class="sortable" data-order="DESC" data-column="JobID"><div>Job ID<i class="arrow arrow-up active"></i></div></th>
									<th class="sortable" data-order="ASC" data-column="JobTitleName"><div>Job Title<i class="arrow arrow-up"></i></div></th>
									<th class="sortable" data-order="ASC" data-column="JobStatusName"><div>Status<i class="arrow arrow-up"></i></div></th>
									<th class="sortable" data-order="ASC" data-column="CreatedDate"><div>Created<i class="arrow arrow-up"></i></div></th>
									<th class="sortable" data-order="ASC" data-column="FirstName"><div>Currently Held By<i class="arrow arrow-up"></i></div></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
						<div class="text-center" id="jobs-loader" style="display: none;">
							<div id="loading"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- New job title modal -->
<div class="modal fade modal-md" id="NewJobModal" tabindex="-1" role="dialog" aria-labelledby="NewJobModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Post a Job</h5>
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
			<form method="POST" action="core/save-jobs.php">
				<div class="modal-body">
					<div class="form-group row mb-2">
						<label for="JobStatusID" class="col-sm-3 col-form-label">Status:</label>
						<div class="col-sm-9">			
							<input class="form-control" type="text" readonly="true" name="JobStatusID" value="Draft">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label for="JobTitleName" class="col-sm-3 col-form-label">Title:</label>
						<div class="col-sm-9">			
							<select class="form-control" name="JobTitleID">				
							<?php while($row = sqlsrv_fetch_array($jt, SQLSRV_FETCH_ASSOC) ){ ?>
								<option value="<?=$row['jobTitleID']?>"><?=$row['JobTitleName']?></option>
							<?php } ?>
							</select>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary" value="new-job" name="submit">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php include("_footer.php"); ?>
<script type="text/javascript">
	$(document).ready(function(){
		getJobs();
	});
</script>
