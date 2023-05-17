<?php include("_header.php");
$kf = getKPIFreq($conn);
$kc = getKPICategories($conn);

$freqs = [];
$cats  = [];
while($row = sqlsrv_fetch_array($kf, SQLSRV_FETCH_ASSOC) ){ $freqs[] = $row;}
while($row = sqlsrv_fetch_array($kc, SQLSRV_FETCH_ASSOC) ){ $cats[] = $row;}

?>
<div class="header">
	<div class="row">
		<div class="col-6">
			<h2>Job Requirements</h2>
		</div>
		<div class="col-6">
		</div>
	</div>
	<div class="bg-white p-3 my-4">
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
						<button class="btn btn-md btn-primary btn-rounded float-end" data-bs-toggle="modal" data-bs-target="#DutyModal">
							<i class="fas fa-plus"></i> Add new Duty
						</button>
					</div>
				</div>
				<table class="table table-primary table-hover tr-link" id="table-duties">
					<thead>
						<tr>
							<th class="sortable" data-order="DESC" data-column="DutyID"><div>Duty ID<i class="arrow arrow-up active"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="DutyName"><div>Duty Name<i class="arrow arrow-up"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="InLib"><div>In Library<i class="arrow arrow-up"></i></div></th>
							<th></th>
			   		</thead>
					<tbody></tbody>
				</table>
				<div class="text-center" id="duties-loader" style="display: none;">
					<div id="loading"></div>
				</div>
			</div>
			<div class="tab-pane" id="projects-panel" role="tabpanel" aria-labelledby="projects-tab">
				<div class="row mb-4">
					<div class="col text-right">
						<button class="btn btn-md btn-primary btn-rounded float-end" data-bs-toggle="modal" data-bs-target="#ProjectModal">
							<i class="fas fa-plus"></i> Add new Project
						</button>
					</div>
				</div>
				<table class="table table-primary table-hover tr-link" id="table-projects">
					<thead>
						<tr>
							<th class="sortable" data-order="DESC" data-column="ProjectID"><div>Project ID<i class="arrow arrow-up active"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="ProjectName"><div>Project Name<i class="arrow arrow-up"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="InLib"><div>In Library<i class="arrow arrow-up"></i></div></th>
							<th></th>
			   		</thead>
					<tbody></tbody>
				</table>
				<div class="text-center" id="projects-loader" style="display: none;">
					<div id="loading"></div>
				</div>
			</div>
			<div class="tab-pane active" id="skills-panel" role="tabpanel" aria-labelledby="skills-tab">
				<div class="row mb-4">
					<div class="col text-right">
						<button class="btn btn-md btn-primary btn-rounded float-end" data-bs-toggle="modal" data-bs-target="#SkillModal">
							<i class="fas fa-plus"></i> Add new Skill
						</button>
					</div>
				</div>
				<table class="table table-primary table-hover tr-link" id="table-skills">
					<thead>
						<tr>
							<th class="sortable" data-order="DESC" data-column="SkillID"><div>Skill ID<i class="arrow arrow-up active"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="SkillName"><div>Skill Name<i class="arrow arrow-up"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="InLib"><div>In Library<i class="arrow arrow-up"></i></div></th>
							<th></th>
			   		</thead>
					<tbody></tbody>
				</table>
				<div class="text-center" id="skills-loader" style="display: none;">
					<div id="loading"></div>
				</div>
			</div>
			<div class="tab-pane" id="kpi-panel" role="tabpanel" aria-labelledby="kpi-tab">
				<div class="row mb-4">
					<div class="col text-right">
						<button class="btn btn-md btn-primary btn-rounded float-end" data-bs-toggle="modal" data-bs-target="#KPIModal">
							<i class="fas fa-plus"></i> Add new KPI
						</button>
					</div>
				</div>
				<table class="table table-primary table-hover tr-link" id="table-kpi">
					<thead>
						<tr>
							<th class="sortable" data-order="DESC" data-column="KPIID"><div>KPI ID<i class="arrow arrow-up active"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="KPITitle"><div>KPI Name<i class="arrow arrow-up"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="CategoryName"><div>Category<i class="arrow arrow-up"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="InLib"><div>In Library<i class="arrow arrow-up"></i></div></th>
							<th></th>
			   		</thead>
					<tbody></tbody>
				</table>
				<div class="text-center" id="kpi-loader" style="display: none;">
					<div id="loading"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- modal duty -->
<div class="modal fade modal-md" id="DutyModal" tabindex="-1" role="dialog" aria-labelledby="DutyModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Add new Duty</h5>
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
			<form method="POST" action="core/save-requirements.php">
				<div class="modal-body">
					<div class="form-group row mb-2">
						<label for="DutyName" class="col-sm-3 col-form-label">Duty Name:</label>
						<div class="col-sm-9">
							<input type="hidden" class="form-control" id="table" name="table" value="Duties">
							<input type="text" class="form-control" id="DutyName" name="DutyName" required="true">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="col-sm-3 col-form-label">In Library:</label>
						<div class="col-sm-9 inlibinputs ptt10">
							<input type="radio" id="InLibYes" name="InLib" value="1">
  							<label for="InLibYes">Yes</label>
							<input type="radio" id="InLibNo" name="InLib" value="0">
  							<label for="InLibNo">No</label>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary" value="new-requirement" name="submit">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- modal project -->
<div class="modal fade modal-md" id="ProjectModal" tabindex="-1" role="dialog" aria-labelledby="ProjectModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Add new Project</h5>
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
			<form method="POST" action="core/save-requirements.php">
				<div class="modal-body">
					<div class="form-group row mb-2">
						<label for="ProjectName" class="col-sm-3 col-form-label">Project Name:</label>
						<div class="col-sm-9">
							<input type="hidden" class="form-control" id="table" name="table" value="Projects">
							<input type="text" class="form-control" id="ProjectName" name="ProjectName" required="true">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="col-sm-3 col-form-label">In Library:</label>
						<div class="col-sm-9 inlibinputs ptt10">
							<input type="radio" id="InLibYes" name="InLib" value="1">
  							<label for="InLibYes">Yes</label>
							<input type="radio" id="InLibNo" name="InLib" value="0">
  							<label for="InLibNo">No</label>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary" value="new-requirement" name="submit">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- modal skills -->
<div class="modal fade modal-md" id="SkillModal" tabindex="-1" role="dialog" aria-labelledby="SkillModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Add new Skill</h5>
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
			<form method="POST" action="core/save-requirements.php">
				<div class="modal-body">
					<div class="form-group row mb-2">
						<label for="SkillName" class="col-sm-3 col-form-label">Skill Name:</label>
						<div class="col-sm-9 ">
							<input type="hidden" class="form-control" id="table" name="table" value="Skills">
							<input type="text" class="form-control" id="SkillName" name="SkillName" required="true">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="col-sm-3 col-form-label">In Library:</label>
						<div class="col-sm-9 inlibinputs ptt10">
							<input type="radio" id="InLibYes" name="InLib" value="1">
  							<label for="InLibYes">Yes</label>
							<input type="radio" id="InLibNo" name="InLib" value="0">
  							<label for="InLibNo">No</label>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary" value="new-requirement" name="submit">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- modal kpi -->
<div class="modal fade modal-md" id="KPIModal" tabindex="-1" role="dialog" aria-labelledby="KPIModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Add new KPI</h5>
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
			<form method="POST" action="core/save-requirements.php">
				<div class="modal-body">
					<div class="form-group row mb-2">
						<label for="KPITitle" class="col-sm-3 col-form-label">KPI Name:</label>
						<div class="col-sm-9">
							<input type="hidden" class="form-control" id="table" name="table" value="KPIs">
							<input type="text" class="form-control" id="KPITitle" name="KPITitle" required="true">
						</div>
					</div>
					<div class="form-group row mb-2">
						<label for="KPITargetNum" class="col-sm-3 col-form-label">Target:</label>
						<div class="col-sm-3">
							<input type="number" class="form-control" id="KPITargetNum" name="KPITargetNum" required="true" min="0">
						</div>
						<label for="KPITargetNum" class="col-sm-1 col-form-label">per:</label>
						<div class="col-sm-5">
							<select class="form-control" name="TargetNumFreqID">
							<?php foreach($freqs as $freq){ ?>
								<option value="<?=$freq['FreqID']?>"><?=$freq['FreqName']?></option>
							<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label for="KPICategoryID" class="col-sm-3 col-form-label">Category</label>
						<div class="col-sm-9">
							<select class="form-control" name="KPICategoryID">
							<?php foreach($cats as $cat){ ?>
								<option value="<?=$cat['KPICategoryID']?>"><?=$cat['CategoryName']?></option>
							<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="col-sm-3 col-form-label">In Library:</label>
						<div class="col-sm-9 pt-2 inlibinputs">
							<input type="radio" id="InLibYes" name="InLib" value="1">
  							<label for="InLibYes">Yes</label>
							<input type="radio" id="InLibNo" name="InLib" value="0">
  							<label for="InLibNo">No</label>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary" value="new-requirement" name="submit">Save</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php include("_footer.php"); ?>
<script type="text/javascript">
	$(document).on('click', '.nav-link', function(){
		loadJobReq($(this).attr('id'));
	});

	$(document).ready(function(){
		loadJobReq($(".nav-link.active").attr('id'));
	});

	function loadJobReq(tab){
		console.log(tab);
		if (tab == 'duties-tab') {
			getJobDuties();
		} else if (tab == 'skills-tab') {
			getJobSkills();
		} else if (tab == 'projects-tab') {
			getJobProjects();
		} else if (tab == 'kpi-tab') {
			getJobKPIs();
		}
	}
</script>