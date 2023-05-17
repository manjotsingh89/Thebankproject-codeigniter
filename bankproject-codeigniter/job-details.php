<?php

if(!isset($_GET['id'])) header("location: index.php"); 
include("_header.php");

$j  = getJobDetails($conn, $_GET['id']);
if(!$j){
	die("Invalid JOB ID");
}

$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === FALSE ? 'http' : 'https';
$domainLink = $protocol . '://' . $_SERVER['HTTP_HOST'];
$ji = getJobInterviews($conn, $_GET['id']);
$ja = getJobApplications($conn, $_GET['id']);
$jr = getJobRequirementsByJobID($conn, $_GET['id']);
$ed = getEmployeeDetails($conn, $_SESSION['EmployeeID']);
$jn = getJobNotes($conn, $_GET['id']);

$d  = getDuties(true);
$p  = getProjects(true);
$s  = getSkills(true);
$k  = getKPIs(true);
$kf = getKPIFreq($conn);
$kc = getKPICategories($conn);
$job_statuses = getJobStatuses($conn);

$employees = [];
$employees_res = getEmployees($conn);
while ($row = sqlsrv_fetch_array($employees_res, 2)) {
	$employees[] = $row;
}

$duties   = [];
$projects = [];
$skills   = [];
$kpis 	  = [];

$dIds = [];
$pIds = [];
$sIds = [];
$kIds = [];

//get all requirements to assign to designated type
while($r = sqlsrv_fetch_array($jr, SQLSRV_FETCH_ASSOC) ){
	if($r['DutyID'] !== NULL){
		$duties[] = $r;
		$dIds[]   = $r['DutyID'];
	}
	if($r['ProjectID'] !== NULL){
		$projects[] = $r;
		$pIds[] = $r['ProjectID'];
	}
	if($r['SkillID'] !== NULL){
		$skills[] = $r;
		$sIds[] = $r['SkillID'];
	}
	if($r['KPIID'] !== NULL){
		$kpis[] = $r;
		$kIds[] = $r['KPIID'];
	}
}

while ($row = sqlsrv_fetch_array($kc, 2)) {
	$cats[] = $row;
}

while($frequency = sqlsrv_fetch_array($kf, 2)) {
	$frequencies[] = $frequency;
}


?>

<div class="header">
	<form id="form-employee-details">
		<!-- hidden employee id -->
		<input type="hidden" value="<?=$_GET['id']?>" id="JobID" name="JobID">

		<div class="row">
			<div class="col-6">
				<h2>Job Details</h2>
			</div>
			<div class="col-3 text-end">
				<?php if(strtolower($j['JobStatusName']) == 'open'){ ?>
				<a class="btn btn-sm btn-primary btn-rounded copy-url" href="<?=$domainLink?>/application.php?id=<?=urlencode(base64_encode('job-id-' . $_GET['id']))?>">Get Application URL</a>
				<?php } ?>
			</div>
			<div class="col-3">
				<div class="form-group row mb-2 d-flex align-items-center">
					<label for="FirstName" class="col-sm-4 col-form-label statuslabel"><b>Status</b></label>
					<div class="col-sm-8 text-right statusvalue">
						<select name="JobStatusID" id="select-job-status" data-job-id="<?=$j["JobID"]?>" class="form-control">
							<?php foreach ($job_statuses as $job_status) {?>
								<option value="<?=$job_status["JobStatusID"]?>" <?=strtolower($j['JobStatusName']) == strtolower($job_status["JobStatusName"]) ? 'selected' : '' ?>><?=$job_status["JobStatusName"]?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-group row mb-2 d-flex align-items-center">
					<label for="LastName" class="col-sm-4 col-form-label filledbylabel"><b>Filled By</b></label>
					<div class="col-sm-8 filledbyvalue">
						<select name="FilledByEmpID" id="select-job-filled-by" data-job-id="<?=$j["JobID"]?>" class="form-control">
							<option <?=$j['FilledByEmpID'] == null ? '' : 'disabled' ?>>Select Filled By</option>
							<?php foreach ($employees as $employee) {?>
								<option value="<?=$employee["EmployeeID"]?>" <?=$j['FilledByEmpID'] == $employee["EmployeeID"] ? 'selected' : (strtolower($j['JobStatusName']) != 'open' ? 'disabled' : '') ?>><?=$employee["FirstName"] . " " . $employee["LastName"] ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="bg-white p-3 my-4">
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#forms-controller" type="button" role="tab" aria-controls="home" aria-selected="true">General Info</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="req-tab" data-bs-toggle="tab" data-bs-target="#req" type="button" role="tab" aria-controls="req" aria-selected="false">Requirements</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="Interviews-tab" data-bs-toggle="tab" data-bs-target="#interviews" type="button" role="tab" aria-controls="interviews" aria-selected="false">Interviews</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="Applications-tab" data-bs-toggle="tab" data-bs-target="#applications" type="button" role="tab" aria-controls="applications" aria-selected="false">Applications</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="Notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab" aria-controls="notes" aria-selected="false">Notes</button>
				</li>
			</ul>
			<div class="tab-content mt-4">
				<div class="tab-pane active" id="forms-controller" role="tabpanel" aria-labelledby="home-tab">
					<div class="form-group row mb-2">
						<label for="FirstName" class="col-sm-2 col-form-label">Date Created:</label>
						<div class="col-sm-4">
							<?=$j['CreatedDate']?>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label for="Email" class="col-sm-2 col-form-label">Date Field:</label>
						<div class="col-sm-3">
							<?=$j['FilledDate']?>
						</div>
					</div>

					<div class="form-group row mb-2">
						<label for="Citizenship" class="col-sm-2 col-form-label">Job Title:</label>
						<div class="col-sm-2">
							<p class="pl-2"><?=$j['JobTitleName']?></p>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="col-sm-2 col-form-label">Description:</label>
						<div class="col-sm-3">
							<p class="pl-2"><?=$j['JobDescription']?></p>
						</div>
					</div>
					<div class="row">
						<label for="Email" class="col-2 col-form-label">Sales: </label>
						<div class="col-8">
							<input type="checkbox" name="JobSales" value="1" <?=$j["JobSales"] ? 'checked' : '' ?> class="form-check-input">
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-6 text-start">
							<a class="btn btn-danger" id="delete-job" data-id="<?=$_GET['id']?>">Delete</a>
							<a class="btn btn-success" id="copy-job" data-id="<?=$_GET['id']?>">Copy</a>
						</div>
						<div class="col-6 text-end">
							<a class="btn btn-warning">Cancel</a>
							<a type="button" class="btn btn-success" id="save-job" data-id="<?=$_GET['id']?>">Save</a>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="req" role="tabpanel" aria-labelledby="req-tab">
					<ul class="nav nav-tabs" id="myTab" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link active" id="skills-tab" data-bs-toggle="tab" data-bs-target="#skills-panel" type="button" role="tab" aria-controls="skills-panel" aria-selected="false">Skills</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="kpi-tab" data-bs-toggle="tab" data-bs-target="#kpi-panel" type="button" role="tab" aria-controls="kpi-panel" aria-selected="false">KPI's</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="duties-tab" data-bs-toggle="tab" data-bs-target="#duties-panel" type="button" role="tab" aria-controls="duties-panel" aria-selected="true">Duties</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects-panel" type="button" role="tab" aria-controls="projects-panel" aria-selected="false">Projects</button>
						</li>
					</ul>
					<div class="tab-content mt-4">
						<div class="tab-pane" id="duties-panel" role="tabpanel" aria-labelledby="duties-tab">
							<div class="row mb-4">
								<div class="col text-right">
									<a class="btn btn-md btn-primary btn-rounded float-end" data-bs-toggle="modal" data-bs-target="#DutyModal">
										<i class="fas fa-plus"></i> Add new Duty
									</a>
								</div>
							</div>
							<table class="table table-primary" id="table-requirements-Duties">
								<thead>
									<tr>
										<th class="d-none">Job Requirement ID</th>
										<!-- <th>Duty ID</th> -->
										<th>Duty Name</th>
										<th></th>
						   		</thead>
								<tbody>
									<?php foreach($duties as $duty){ ?>
									<tr id="<?=$duty['JobRequirementID']?>">
										<td class="d-none"><?=$duty['JobRequirementID']?></td>
										<!-- <td data-column="DutyID"><?=$duty['DutyID']?></td> -->
										<td data-column="DutyName"><?=$duty['DutyName']?></td>
										<td><button style="float: right;" type="button" class="btn btn-sm btn-danger delete-job-requirement" data-requirement-id="<?=$duty['JobRequirementID']?>">Delete</button></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<div class="tab-pane" id="projects-panel" role="tabpanel" aria-labelledby="projects-tab">
							
							<div class="row mb-4">
								<div class="col text-right">
									<a class="btn btn-md btn-primary btn-rounded float-end" data-bs-toggle="modal" data-bs-target="#ProjectModal">
										<i class="fas fa-plus"></i> Add new Project
									</a>
								</div>
							</div>
							<table class="table table-primary" id="table-requirements-Projects">
								<thead>
									<tr>
										<th class="d-none">Job Requirement ID</th>
										<th>Project Name</th>
										<th>Project Target</th>
										<th>Project Quarter</th>
										<th>Project Year</th>
										<th></th>
						   		</thead>
								<tbody>
									<?php foreach($projects as $project){ ?>
									<tr id="<?=$project['JobRequirementID']?>">
										<td class="d-none"><?=$project['JobRequirementID']?></td>
										<td data-column="ProjectName"><?=$project['ProjectName']?></td>
										<td data-column="ProjectTarget"><input type="number" class="form-control job-requirement-alt" name="ProjectTarget" value="<?=$project['ProjectTarget']?>"></td>
										<td data-column="ProjectQuarter"><input type="number" class="form-control job-requirement-alt" name="ProjectQuarter" value="<?=$project['ProjectQuarter']?>"></td>
										<td data-column="ProjectYear"><input type="number" class="form-control job-requirement-alt" name="ProjectYear" value="<?=$project['ProjectYear']?>"></td>
										<td><a type="button" style="float: right;" class="btn btn-sm btn-danger delete-job-requirement" data-requirement-id="<?=$project['JobRequirementID']?>">Delete</a></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<div class="tab-pane active" id="skills-panel" role="tabpanel" aria-labelledby="skills-tab">

							<div class="row mb-4">
								<div class="col text-right">
									<a class="btn btn-md btn-primary btn-rounded float-end" data-bs-toggle="modal" data-bs-target="#SkillModal">
										<i class="fas fa-plus"></i> Add new Skill
									</a>
								</div>
							</div>
							<table class="table table-primary" id="table-requirements-Skills">
								<thead>
									<tr>
										<th class="d-none">Job Requirement ID</th>
										<!-- <th>Skill ID</th> -->
										<th>Skill Name</th>
										<td></td>
						   		</thead>
								<tbody>
									<?php foreach($skills as $skill){ ?>
									<tr id="<?=$skill['JobRequirementID']?>">
										<td class="d-none"><?=$skill['JobRequirementID']?></td>
										<!-- <td data-column="SkillID"><?=$skill['SkillID']?></td> -->
										<td data-column="SkillName"><?=$skill['SkillName']?></td>
										<td><a style="float: right;" type="button" class="btn btn-sm btn-danger delete-job-requirement" data-requirement-id="<?=$skill['JobRequirementID']?>">Delete</a></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<div class="tab-pane" id="kpi-panel" role="tabpanel" aria-labelledby="kpi-tab">
							<div class="row mb-4">
								<div class="col text-right">
									<a class="btn btn-md btn-primary btn-rounded float-end" data-bs-toggle="modal" data-bs-target="#KPIModal">
										<i class="fas fa-plus"></i> Add new KPI
									</a>
								</div>
							</div>
							<table class="table table-primary" id="table-requirements-KPIs">
								<thead>
									<tr>
										<th class="d-none">Job Requirement ID</th>
										<!-- <th>KPI ID</th> -->
										<th>KPI Name</th>
										<th>Category</th>
										<th>Target</th>
										<th></th>
										<th>Target Frequency</th>
										<th></th>
						   		</thead>
								<tbody>
									<?php foreach($kpis as $kpi){ ?>
									<tr id="<?=$kpi['JobRequirementID']?>">
										<td class="d-none"><?=$kpi['JobRequirementID']?></td>
										<!-- <td data-column="KPIID"><?=$kpi['KPIID']?></td> -->
										<td data-column="KPITitle"><?=$kpi['KPITitle']?></td>
										<td data-column="CategoryName"><?=$kpi['CategoryName']?></td>
										<td data-column="KPITargetNum"><input type="number" name="KPITargetNum" class="form-control job-requirement-alt" value="<?=$kpi['KPITargetNum']?>"></td>
										<td data-column="per">Per</td>
										<td data-column="TargetNumFreqID">
											<select name="TargetNumFreqID" class="form-control job-requirement-alt">
												<option>Select Frequency</option>
												<?php foreach($frequencies as $frequency) {?>
												<option <?=$kpi["TargetNumFreqID"] == $frequency["FreqID"] ? 'selected' : ''?> value="<?=$frequency["FreqID"]?>"><?=$frequency["FreqName"]?></option>
												<?php } ?>
											</select>
										</td>
										<td><a style="float: right;" type="button" class="btn btn-sm btn-danger delete-job-requirement" data-requirement-id="<?=$kpi['JobRequirementID']?>">Delete</a></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
					</div>
				<div class="tab-pane" id="applications" role="tabpanel" aria-labelledby="Applications-tab">
					<table class="table table-primary" id="table-applications">
						<thead>
							<tr>
								<th>Application ID</th>
								<th>Full Name</th>
								<th>Email</th>
								<th>Telephone Number</th>
								<th>Interviews</th>
								<th></th>
								<?php if($ed['Interviewer'] == 1 && strtolower($j['JobStatusName']) == 'open') { ?>
								<th></th>
								<?php } ?>
				   		</thead>
						<tbody>
							<?php while($row = sqlsrv_fetch_array($ja, SQLSRV_FETCH_ASSOC) ){ ?>
							<tr data-link="view-application.php?id=<?=$row['ApplicationID']?>">
							<td><?=$row['ApplicationID']?></td>
							<td><?=$row['FirstName'].' '.$row['LastName']?></td>
							<td><?=$row['Email']?></td>
							<td><?=$row['TelephoneNumber']?></td>
							<td><?=$row['InterviewCount'] === NULL ? '0' : $row['InterviewCount']?></td>
							<td><a href="view-application.php?id=<?=$row['ApplicationID']?>" class='btn btn-primary btn-sm' target="_blank">View Application</a></td></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				<div class="tab-pane" id="interviews" role="tabpanel" aria-labelledby="Interviews-tab">
					<table class="table table-primary table-hover tr-link" id="table-interviews">
						<thead>
							<tr>
								<th>Full Name</th>
								<th>Interviewer</th>
								<th>Actions</th>
				   		</thead>
						<tbody>
							<?php while($interview = sqlsrv_fetch_array($ji, SQLSRV_FETCH_ASSOC) ){ ?>
							<tr data-link="view-interview.php?id=<?=$interview['InterviewID']?>">
								<td width="300px"><?=$interview['IntervieweeFirstName'] . ' ' . $interview['IntervieweeLastName']?></td>
								<td width="300px"><?=$interview['InterviewerFirstName'] . ' ' . $interview['InterviewerLastName']?></td>
								<td width="300px"><a href="view-interview.php?id=<?=$interview['InterviewID']?>" class="btn btn-primary btn-sm">View Interview</a></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				<div class="tab-pane" id="docs" role="tabpanel" aria-labelledby="docs-tab"></div>
				<div class="tab-pane" id="other-docs" role="tabpanel" aria-labelledby="other-docs-tab"></div>
				<div class="tab-pane" id="notes" role="tabpanel" aria-labelledby="Notes-tab">
					<div class="row mb-4">
						<div class="col text-right">
							<a class="btn btn-md btn-primary btn-rounded float-end" data-bs-toggle="modal" data-bs-target="#NotesModal">
								<i class="fas fa-plus"></i> Add note
							</a>
						</div>
					</div>
					<table class="table table-primary " id="table-notes">
						<thead>
							<tr>
								<th>Note ID</th>
								<th>Note</th>
								<th>Created Date</th>
				   		</thead>
						<tbody>
							<?php while($row = sqlsrv_fetch_array($jn, SQLSRV_FETCH_ASSOC) ){ ?>
							<tr>
								<td width="100px"><?=$row['JobNoteID'];?></td>
								<td>
									<input type="text" value="<?=$row['Note']?>" class="borderless editable" data-id="<?=$row['JobNoteID']?>" data-table="JobNotes" data-field="Note" data-idfield="JobNoteID">
								</td>
							<td><?=$row['CreatedDate'];?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</form>
</div>
<!-- modal duty -->
<div class="modal fade" id="DutyModal" tabindex="-1" role="dialog" aria-labelledby="DutyModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">                                           
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
			<div class="modal-body">
				<form method="POST" class="create-job-requirement" data-table="Duties">
					<div class="form-group row mb-2 px-2">
						<label for="DutyName" class="col-sm-2 col-form-label">Duty Name:</label>
						<div class="col-sm-3">
							<input type="hidden" id="JobID" name="JobID" value="<?=$_GET['id']?>">
							<input type="text" class="form-control" id="DutyName" name="DutyName" required="true" placeholder="Enter duty name">
						</div>
						<label class="col-sm-2 col-form-label">In Library:</label>
						<div class="col-sm-3 pt-2 text-right inlibinputs">
							<input type="radio" id="InLibYes" name="InLib" value="1" checked>
  							<label for="InLibYes">Yes</label>
							<input type="radio" id="InLibNo" name="InLib" value="0">
  							<label for="InLibNo">No</label>
						</div>
						<div class="col-sm-2 pt-2 text-right">
						<button type="submit" class='btn btn-primary btn-sm btn-sm w-100' value="new-requirement" name="submit">
								<i class='fa fa-plus'></i> Add to Job
							</button>
						</div>
					</div>
				</form>
				<hr>
				<table class="table table-primary table-hover tr-link mt-4" id="table-duties">
					<thead>
						<tr>
							<th>Duty ID</th>
							<th>Duty Name</th>
							<th></th>
						</tr>
			   		</thead>
					<tbody>
						<?php while($row = sqlsrv_fetch_array($d, SQLSRV_FETCH_ASSOC) ) { ?>
						<tr id="DutyID-<?=$row['DutyID']?>" <?=in_array($row['DutyID'], $dIds) ? 'style="display: none;"' : '' ?>>
							<td><?=$row['DutyID']?></td>
							<td><?=$row['DutyName']?></td>
							<td align="center">
								<a class='btn btn-primary add-job-requirement-btn btn-sm' data-value="<?=$row['DutyID']?>" data-column="DutyID" data-job-id="<?=$_GET['id']?>">
									<i class='fa fa-plus'></i> Add to Job
								</a>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- modal project -->
<div class="modal fade modal-md" id="ProjectModal" tabindex="-1" role="dialog" aria-labelledby="ProjectModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
			<div class="modal-body">
				<form method="POST" class="create-job-requirement" data-table="Projects">
					<div class="form-group row mb-2 px-2">
						<label for="ProjectName" class="col-sm-2 col-form-label">Project Name:</label>
						<div class="col-sm-3">
							<input type="hidden" id="JobID" name="JobID" value="<?=$_GET['id']?>">
							<input type="text" class="form-control" id="ProjectName" name="ProjectName" required="true" placeholder="Enter project name">
						</div>
						<label class="col-sm-2 col-form-label">In Library:</label>
						<div class="col-sm-3 pt-2 text-right inlibinputs">
							<input type="radio" id="InLibYes" name="InLib" value="1" checked>
  							<label for="InLibYes">Yes</label>
							<input type="radio" id="InLibNo" name="InLib" value="0">
  							<label for="InLibNo">No</label>
						</div>
						<div class="col-sm-2">
							<button type="submit" class='btn btn-primary btn-sm btn-sm w-100' value="new-requirement" name="submit">
								<i class='fa fa-plus'></i> Add to Job
							</button>
						</div>
					</div>
				</form>
				<table class="table table-primary table-hover tr-link mt-4" id="table-projects">
					<thead>
						<tr>
							<th>Project ID</th>
							<th>Project Name</th>
							<th></th>
			   		</thead>
					<tbody>
						<?php while($row = sqlsrv_fetch_array($p, SQLSRV_FETCH_ASSOC) ){ ?>
						<tr id="ProjectID-<?=$row['ProjectID']?>" <?=in_array($row['ProjectID'], $pIds) ? 'style="display: none;"' : '' ?>>
							<td><?=$row['ProjectID']?></td>
							<td><?=$row['ProjectName']?></td>
							<td align="center">
								<a class='btn btn-primary add-job-requirement-btn btn-sm' data-value="<?=$row['ProjectID']?>" data-column="ProjectID" data-job-id="<?=$_GET['id']?>">
									<i class='fa fa-plus'></i> Add to Job
								</a>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- modal skills -->
<div class="modal fade modal-md" id="SkillModal" tabindex="-1" role="dialog" aria-labelledby="SkillModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
			<form method="POST" class="create-job-requirement" data-table="Skills">
				<div class="modal-body">
					<div class="form-group row mb-2 px-2">
						<label for="SkillName" class="col-sm-2 col-form-label">Skill Name:</label>
						<div class="col-sm-3">
							<input type="hidden" id="table" name="table" value="Skills">
							<input type="hidden" id="InLib" name="InLib" value="1">
							<input type="hidden" id="type" name="type" value="SkillID">
							<input type="hidden" id="JobID" name="JobID" value="<?=$_GET['id']?>">
							<input type="text" class="form-control" id="SkillName" name="SkillName" required="true" placeholder="Enter skill name">
						</div>
						<label class="col-sm-2 col-form-label">In Library:</label>
						<div class="col-sm-3 pt-2 text-right inlibinputs">
							<input type="radio" id="InLibYes" name="InLib" value="1" checked>
  							<label for="InLibYes">Yes</label>
							<input type="radio" id="InLibNo" name="InLib" value="0">
  							<label for="InLibNo">No</label>
						</div>
						<div class="col-sm-2 pt-2 text-right">
						<button type="submit" class='btn btn-primary btn-sm btn-sm w-100' value="new-requirement" name="submit">
								<i class='fa fa-plus'></i> Add to Job
							</button>
							</div>
							
					</div>
					<div class="form-group row mb-2">
						<div class="col-sm-3 offset-sm-9">
							
						</div>
					</div>

					<table class="table table-primary table-hover tr-link mt-4" id="table-projects">
						<thead>
							<tr>
								<th>Skill ID</th>
								<th>Skill Name</th>
								<th></th>
				   		</thead>
						<tbody>
							<?php while($row = sqlsrv_fetch_array($s, SQLSRV_FETCH_ASSOC) ){ ?>
							<tr id="SkillID-<?=$row['SkillID']?>" <?=in_array($row['SkillID'], $sIds) ? 'style="display: none;"' : '' ?>>
								<td><?=$row['SkillID']?></td>
								<td><?=$row['SkillName']?></td>
								<td align="center">
									<a class='btn btn-primary add-job-requirement-btn btn-sm' data-value="<?=$row['SkillID']?>" data-column="SkillID" data-job-id="<?=$_GET['id']?>">
										<i class='fa fa-plus'></i> Add to Job
									</a>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- modal kpi -->
<div class="modal fade modal-md" id="KPIModal" tabindex="-1" role="dialog" aria-labelledby="KPIModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Add new KPI</h5>
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
			<div class="modal-body">
				<form method="POST" class="create-job-requirement" data-table="KPIs">
					<div class="form-group row mb-2">
						<label for="KPITitle" class="col-sm-2 col-form-label">Name/Title:</label>
						<div class="col-sm-4">
							<input type="hidden" id="JobID" name="JobID" value="<?=$_GET['id']?>">
							<input type="text" class="form-control" id="KPITitle" name="KPITitle" required="true" placeholder="Enter KPI title">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label for="KPICategoryID" class="col-sm-2 col-form-label">Category</label>
						<div class="col-sm-4">
							<select class="form-control" name="KPICategoryID">
							<?php foreach($cats as $cat){ ?>
								<option value="<?=$cat['KPICategoryID']?>"><?=$cat['CategoryName']?></option>
							<?php } ?>
							</select>
						</div>
						<label class="col-sm-2 col-form-label">In Library:</label>
						<div class="col-sm-4 pt-2 text-right inlibinputs">
							<input type="radio" id="InLibYes" name="InLib" value="1" checked>
  							<label for="InLibYes">Yes</label>
							<input type="radio" id="InLibNo" name="InLib" value="0">
  							<label for="InLibNo">No</label>
						</div>
					</div>
					<div class="form-group row mb-2">
						<div class="col-sm-5">
							<button type="submit" class='btn btn-primary btn-sm btn-sm w-100' value="new-requirement" name="submit">
								<i class='fa fa-plus'></i> Add to Job
							</button>
						</div>
					</div>
				</form>
				<table class="table table-primary table-hover tr-link mt-4" id="table-kpis">
					<thead>
						<tr>
							<th>KPI ID</th>
							<th>KPI Name</th>
							<th>Category</th>
							<th></th>
			   		</thead>
					<tbody>
						<?php while($row = sqlsrv_fetch_array($k, SQLSRV_FETCH_ASSOC) ){  ?>
						<tr id="KPIID-<?=$row['KPIID']?>" <?=in_array($row['KPIID'], $kIds) ? 'style="display: none;"' : '' ?>>
							<td><?=$row['KPIID']?></td>
							<td><?=$row['KPITitle']?></td>
							<td><?=$row['CategoryName']?></td>
							<td align="center">
								<a class='btn btn-primary add-job-requirement-btn btn-sm' data-value="<?=$row['KPIID']?>" data-column="KPIID" data-job-id="<?=$_GET['id']?>">
									<i class='fa fa-plus'></i> Add to Job
								</a>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<!-- modal notes -->
<div class="modal fade modal-md" id="NotesModal" tabindex="-1" role="dialog" aria-labelledby="NotesModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Add new note</h5>
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
			<form method="POST" action="core/save-notes.php">
				<div class="modal-body">
					<div class="form-group mb-2">
						<input type="hidden" class="form-control" id="EmployeeID" name="JobID" value="<?=$_GET['id']?>">
						<input type="hidden" class="form-control" id="CreatedDate" name="CreatedDate" value="<?=date('Y-m-d h:i:s');?>">
						<input type="hidden" class="form-control" id="table" name="table" value="JobNotes">
						<label for="Note" class="col-form-label">Note:</label>
						<textarea type="text" class="form-control" id="Note" name="Note"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary" value="new-note" name="submit">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>


<!-- modal copy job url -->
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

<?php include("_footer.php"); ?>
<script type="text/javascript">
	$("#select-job-status").width($("#select-job-filled-by").width());
	$("input").addClass('edit-field');
	$("#JobDescription").click(function(){
		$(this).hide();
		$("input[name=JobDescription]").show();
	});

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