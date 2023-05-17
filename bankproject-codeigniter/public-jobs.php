<?php
session_start();
if (!isset($_SESSION['EmployeeID'])){
	header("Location: login.php"); 
	exit();
}

include("core/database.php");
include("core/applications-employees-functions.php");
include("core/jobs-functions.php");
include("core/helpers.php");
$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === FALSE ? 'http' : 'https';
$domainLink = $protocol . '://' . $_SERVER['HTTP_HOST'];

	$j  = getJobs('OPEN');
	$jt = getJobTitles();
	$employees = [];
	$employees_res = getEmployees($conn);
	while ($row = sqlsrv_fetch_array($employees_res, 2)) {
		$employees[] = $row;
	}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>TAB Global</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="img/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/bootstrap.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <link href="css/incipit.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="css/alertify.min.css">
        <link href="css/fontawesome/css/all.css" rel="stylesheet"/>
        <link href="css/font.css" rel="stylesheet"/>
        <link rel="stylesheet" type="text/css" href="css/datatables.css">

    </head>
    <body>
        <div class="d-flex" id="wrapper">
            <!-- Sidebar-->
            <div class="border-end bg-white" id="sidebar-wrapper">
                <div class="sidebar-heading text-center">
                	<a href="/"><img src="img/logo_sm.png" width="80"></a>
                </div>
            </div>
            <!-- Page content wrapper-->
            <div id="page-content-wrapper">

                <!-- Page content-->
                <div class="container-fluid p-4">
					<div class="header">
						<div class="row">
							<div class="col-6">
								<h2>Public Jobs</h2>
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
											<table class="table table-primary table-hover">
												<thead>
													<tr>
														<th class="p-2">Job Title</th>
														<th class="p-2">Actions</th>
													</tr>
												</thead>
												<tbody>
												<?php $jobs = false; while($row = sqlsrv_fetch_array($j, SQLSRV_FETCH_ASSOC) ){ $jobs = true; ?>
												<tr data-link="application.php?id=<?=$row['JobID']?>">
													<td><?=$row['JobTitleName']?></td>
													<td>
														<a class="btn btn-primary copy-url" href="<?=$domainLink?>/application.php?id=<?= urlencode(base64_encode('job-id-' . $row['JobID'])) ?>">Get Application URL</a>
													</td>
												</tr>
												<?php } 
												if (!$jobs) { ?>
													<tr>
														<td colspan="2" class="text-warning text-center p-3">No Open JOBs available.</td>
													</tr>
												<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div class="tab-pane" id="int-lists" role="tabpanel" aria-labelledby="int-tab">Interviews List</div>
								<div class="tab-pane" id="apps-lists" role="tabpanel" aria-labelledby="apps-tab">Applications Lists</div>
							</div>
						</div>
	                </div>
	            </div>
	        </div>
	    </div>


	    <div class="modal fade modal-md" id="JobEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="JobEmployeeLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Copy Job Link</h5>
		        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		      		</div>
					<div class="modal-body">
						<div class="d-flex align-items-center mb-4">
							<input type="radio" name="job-employee" value="new" checked>New&nbsp;&nbsp;
							<input type="radio" name="job-employee" value="existing">Existing
						</div>
						<select name="select-existing-employee" style="display: none;" class="form-control mb-2">
							<option value="0">Select Applicant</option>
							<?php foreach ($employees as $employee) {?>
								<option value="<?= urlencode(base64_encode('employee-id-' . $employee["EmployeeID"])) ?>"><?=$employee["FirstName"] . " " . $employee["LastName"] ?></option>
							<?php } ?>
						</select>
						<div class="input-group">
							<input type="text" name="link" class="form-control">
							<span class="input-group-text" style="cursor: pointer;" id="copy"><img src="/icons/copy-solid.svg" style="height: 20px;width: 20px;"></span>
						</div>
						<input type="hidden" name="base_link" class="form-control">
					</div>
				</div>
			</div>
		</div>


        <!-- Core theme JS-->
        <script src="build/js/intlTelInput.js"></script> 
        <script type="text/javascript" src="js/jquery-3.6.0.min.js"></script>
        <script type="text/javascript" src="js/popper.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/scripts.js"></script>
        <script type="text/javascript" src="js/alertify.min.js"></script>
        <script type="text/javascript" src="js/datatables.js"></script>
        <script type="text/javascript" src="js/incipit/incipit.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
        <script type="text/javascript">
			function setLink(){
				let type = $("input[name=job-employee]:checked").val();
				let base_link = $("#JobEmployeeModal input[name=base_link]").val();
				console.log(type);
				if (type == 'new') {
			    	$("select[name=select-existing-employee]").hide();
			    	$("#JobEmployeeModal input[name=link]").val(base_link);
			    }
			    else {
			    	$("select[name=select-existing-employee]").show();
			    	let EmployeeID = $("select[name=select-existing-employee]").val();
			    	$("#JobEmployeeModal input[name=link]").val(`${base_link + (EmployeeID != 0 ? '&e='+EmployeeID : '')}`);
			    }
			}

        	$(document).on('click', '.copy-url', function(event){
			    event.preventDefault();
			    $("#JobEmployeeModal input[name=base_link]").val($(this).attr('href'));
			    setLink();
			    $("#JobEmployeeModal").modal('show');
			});

			$(document).on('change', 'input[name=job-employee]', function(event){
			    setLink();
			});

			$(document).on('click', '#copy', function(){
			    navigator.clipboard.writeText($("#JobEmployeeModal input[name=link]").val());
			    alertify.success("Copied");
			});

			$(document).on('change', 'select[name=select-existing-employee]', function(){
			    setLink();
			});
        </script>
    </body>
</html>
