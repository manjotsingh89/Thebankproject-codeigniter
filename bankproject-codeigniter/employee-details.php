<?php include("_header.php");

if(!isset($_GET['id']) || $_GET['id'] == "") header("location: index.php"); 

$e    = getEmployeeDetails($conn, $_GET['id']);
if (!$e) {
	die('Invalid Employee ID');
}


$salary = getEmployeeSalary($_GET['id']);
$j    = getJobs();
$statuses   = getStatuses($conn);
$current_job = getEmployeeJob($conn, $_GET['id']);
$employees = getEmployees($conn);

$interviews = [];

$primary_application = getPrimaryApplication($conn, $_GET['id']);
$_SESSION['ApplicantID'] = $_GET['id'];
$_SESSION['ApplicationID'] = $primary_application['ApplicationID'] ?? null;

$el   = getEducationalDetailsLevel($conn);
$educational_levels = [];
while($row = sqlsrv_fetch_array($el, SQLSRV_FETCH_ASSOC) ){
	$educational_levels[] = $row;
}

$ens = NULL;
if (isset($primary_application) && $primary_application != NULL) {
	$pa    = getApplicationDetails($conn, $primary_application['ApplicationID']);
	$aeds   = getApplicationEducationalDetails($conn, $primary_application['ApplicationID']);
	$educational_details = [];
	while ($ed = sqlsrv_fetch_array($aeds, 2)) {
		$educational_details[] = $ed;
	}
	$ens  = getApplicationNationalServices($conn, $primary_application['ApplicationID']);
	$eeh  = getApplicationEmploymentHistory($conn, $primary_application['ApplicationID']);
	$employemt_histories = [];
	while ($eh = sqlsrv_fetch_array($eeh, 2)) {
		$employemt_histories[] = $eh;
	}
	$ers   = getApplicationReferences($conn, $primary_application['ApplicationID']);
	$application_references = [];
	while ($er = sqlsrv_fetch_array($ers, 2)) {
		$application_references[] = $er;
	}
}

$appIds = [];
// var_dump($e);
if(!$e){
	header("Location:index.php");
}


?>

<div class="header">
	<!-- hidden employee id -->
	<input type="hidden" value="<?= $_GET['id'] ?>" id="EmployeeID" name="EmployeeID">

	<div class="row" id="employee-general-detail">
		<div class="col-4">
			<h2>Employee Details</h2>
			<h6><?=$e['FirstName'] . ' ' . $e['LastName']?></h6>
		</div>
		<div class="col-8">
			<div class="row">
				<!-- Start Section by Haseeb -->
				<div class="col-5">
					<div class="form-group row mb-2">
						<label class="col-sm-5 col-form-label">Supervisor:</label>
						<div class="col-sm-7 text-right">
							<select class="form-control" id="CurrentSupervisorSelect" data-employee-id="<?=$e["EmployeeID"]?>" name="CurrentSupervisorID">
								<option>Select Supervisor</option>
								<?php while($employee = sqlsrv_fetch_array($employees, SQLSRV_FETCH_ASSOC) ){ ?>
								<option value="<?=$employee['EmployeeID']?>" <?=$employee['EmployeeID'] == $e["CurrentSupervisorID"] ? 'selected' : ''?>><?=$employee["FirstName"] . ' ' . $employee["LastName"]?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label class="col-sm-5 col-form-label">Next Review Date:</label>
						<div class="col-sm-7 text-right">
							<input type="date" name="NextReviewDate" data-employee-id="<?php echo $_GET['id'] ?>" value="<?php echo $e['NextReviewDate'] ?>" class="form-control">
						</div>
					</div>
					<?php if ($_GET['id'] == $_SESSION['EmployeeID'] && strtolower($e["Status"]) == 'active' && $e['NextReviewDate'] != null && $current_job != null && dateDifference(date('Y-m-d'), $e['NextReviewDate']) <= 10) { ?>
					<div class="form-group row mb-2">
						<div class="offset-7 col-5">
							<button id="start-review" type="button" data-employee-id="<?php echo $_GET['id'] ?>" class="btn btn-primary form-control">Start Review</button>
						</div>
					</div>
					<?php } ?>
				</div>
				<!-- End Section by Haseeb -->
				<div class="col-7">
					<div class="form-group row mb-2">
						<label for="FirstName" class="col-sm-3 col-form-label">Status:</label>
						<div class="col-sm-9 text-right">
							<select class="form-control" id="EmployeeStatusSelect" name="EmployeeStatusID" data-employee-id="<?=$e["EmployeeID"]?>">
								<option>Select Employee Status</option>
								<?php while ($status = sqlsrv_fetch_array($statuses, 2)) {?>
								<option value="<?=$status["EmployeeStatusID"]?>" <?=$e['EmployeeStatusID'] == $status["EmployeeStatusID"] ? 'selected="selected"' : ""?>><?=$status["EmployeeStatusName"]?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group row mb-2">
						<label for="LastName" class="col-sm-3 col-form-label">Current Job:</label>
						<div class="col-sm-9">
							<select class="form-control" id="FilledByEmpID" name="FilledByEmpID" data-employee-id="<?=$e["EmployeeID"]?>">
								<option>Select Current Job</option>
								<?php while ($job = sqlsrv_fetch_array($j, 2)) {?>
								<option value="<?=$job["JobID"]?>" <?=$current_job && $current_job['JobID'] == $job["JobID"] ? 'selected="selected"' : ""?>><?=$job["JobTitleName"]?></option>
								<?php } ?>
							</select>
						</div>
					</div>
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
			<button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="false">Employee Details</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="applications-tab" data-id="<?=$e['EmployeeID']?>" data-bs-toggle="tab" data-bs-target="#applications" type="button" role="tab" aria-controls="applications" aria-selected="false">Applications</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="interviews-tab" data-id="<?=$e['EmployeeID']?>" data-bs-toggle="tab" data-bs-target="#interviews" type="button" role="tab" aria-controls="interviews" aria-selected="false">Interviews</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs" type="button" role="tab" aria-controls="docs" aria-selected="false">Signed Docs</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="other-docs-tab" data-bs-toggle="tab" data-bs-target="#other-docs" type="button" role="tab" aria-controls="other-docs" aria-selected="false">Suppoting Documents</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="reviews-tab" data-id="<?=$e['EmployeeID']?>" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">Reviews</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="notes-tab" data-id="<?=$e['EmployeeID']?>" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab" aria-controls="notes" aria-selected="false">Notes</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="salary-tab" data-bs-toggle="tab" data-bs-target="#salary" type="button" role="tab" aria-controls="salary" aria-selected="false">Salary</button>
		</li>
	</ul>
		<div class="tab-content mt-4">
			<div class="tab-pane active" id="forms-controller" role="tabpanel" aria-labelledby="home-tab">
				<form id="form-employee-details">
					<!--personal details section-->
					<input type="hidden" value="<?= $_GET['id'] ?>" id="EmployeeID" name="EmployeeID">
 					<section id="form-section-1" class="p-4 bg-light" data-position="1" data-table="Employees">
							<div class="form-group row mb-2">
								<label for="FirstName" class="col-sm-2 col-form-label">First Name:</label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="FirstName" name="FirstName" value="<?=$e['FirstName']?>">
								</div>
								<label for="LastName" class="col-sm-2 col-form-label">Last Name:</label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="LastName" name="LastName" value="<?=$e['LastName']?>">
								</div>
							</div>
							<div class="form-group row mb-2">
								<label for="Email" class="col-sm-2 col-form-label">Email:</label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="Email" name="Email" value="<?=$e['Email']?>">
								</div>
								<label for="PassportNumber" class="col-sm-2 col-form-label passport">NRIC No (Colour)/Passport No:</label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="PassportNumber" name="PassportNumber" value="<?=$e['PassportNumber']?>">
								</div>
							</div>

							<div class="form-group row mb-2">
								<label for="Citizenship" class="col-sm-2 col-form-label">Citizenship:</label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="Citizenship" name="Citizenship" value="<?=$e['Citizenship']?>">
								</div>
								<label for="Gender" class="col-sm-2 col-form-label">Gender:</label>
								<div class="col-sm-4">
									<select class="form-control" id="Gender" name="Gender">
										<option value="Male" <?=$e['Gender'] == "Male" ? 'selected="selected"' : ""?>>Male</option>
										<option value="Female" <?=$e['Gender'] == "Female" ? 'selected="selected"' : ""?>>Female</option>
									</select>
								</div>
								
							</div>

							<div class="form-group row mb-2">
							<label for="TelephoneNumber" class="col-sm-2 col-form-label">Tel:</label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="TelephoneNumber" name="TelephoneNumber" value="<?=$e['TelephoneNumber']?>">
								</div>
								
								<label for="Salary" class="col-sm-2 col-form-label">View salary:</label>
								<div class="col-sm-4">
									<input type="checkbox" class="form-check-input" id="Salary" name="Salary" <?=$e['Salary'] == 1 ? 'checked' : ''?>>
								</div>
							</div>

							<div class="form-group row mb-2">
								<label for="Interviewer" class="col-sm-2 col-form-label">Interviewer:</label>
								<div class="col-sm-4">
									<input type="checkbox" class="form-check-input" id="Interviewer" name="Interviewer" <?=$e['Interviewer'] == 1 ? 'checked' : ''?>>
								</div>
							</div>

							
							<hr>
							<div class="row">
								<div class="col-6 text-start">
									<a type="button" class="btn btn-danger" id="delete-employee" data-id="<?=$e['EmployeeID']?>">Delete Employee</a>
								</div>
								<div class="col-6 text-end">
									<a class="btn btn-warning">Cancel</a>
									<a class="btn btn-success" id="save-employee" data-id="<?=$e['EmployeeID']?>">Save</a>
								</div>
							</div>
					</section>
				</form>
			</div>
			<div class="tab-pane" id="details" role="tabpanel" aria-labelledby="details-tab">
				<section id="form-section-1" class="p-4 bg-light" data-position="1" data-table="Applications">
					<form id="form-employee-1">
						<h5 class="my-4">
							<h3>Personal Particulars</h3>
						</h5>
						<div class="form-group row mb-2">
							<label for="FirstName" class="col-sm-2 col-form-label" >First Name:</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="FirstName" name="FirstName" value="<?=$pa['FirstName'] ?? ''?>" required placeholder="Enter first name">
							</div>
							<label for="LastName" class="col-sm-1 col-form-label">Last Name:</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="LastName" name="LastName" value="<?=$pa['LastName'] ?? ''?>" required placeholder="Enter last name">
							</div>
							<label for="Email" class="col-sm-1 col-form-label">Personal Email:</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="Email" name="Email" value="<?=$pa['Email'] ?? ''?>" required placeholder="you@example.com">
							</div>
						</div>
						<div class="form-group row mb-2">
							<label for="Address" class="col-sm-2 col-form-label">Address:</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="address" name="Address" value="<?=$pa['Address'] ?? ''?>" required placeholder="Enter home address">
							</div>
							<label for="Address" class="col-sm-1 col-form-label">City:</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="city" name="City" value="<?=$pa['City'] ?? ''?>" required placeholder="Enter city">
							</div>
							<label for="Address" class="col-sm-1 col-form-label">State:</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="state" name="State" value="<?=$pa['State'] ?? ''?>" required placeholder="Enter state">
							</div>
						</div>

						<div class="form-group row mb-2">
							<label for="TelephoneNumber" class="col-sm-2 col-form-label">Tel:</label>
							<div class="col-sm-2">
								<input type="tel" class="form-control phone" id="TelephoneNumber" required name="TelephoneNumber" value="<?=$pa['TelephoneNumber'] ?? '' == "" ? "" : "+".$pa['TelephoneNumber'] ?? ''?>">
							</div>
							<label for="PagerNumber" class="col-sm-2 col-form-label">H/p No/Pager:</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="PagerNumber" name="PagerNumber" value="<?=$pa['PagerNumber'] ?? ''?>" placeholder="Enter H/p No/Pages">
							</div>
							<label for="Email" class="col-sm-1 col-form-label">Company Email:</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="CompanyEmail" name="CompanyEmail" value="<?=$pa['CompanyEmail'] ?? ''?>" required placeholder="you@example.com">
							</div>
						</div>

						<div class="form-group row mb-2">
							<label for="PassportNumber" class="col-sm-2 col-form-label">NRIC No (Colour)/Passport No:</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="PassportNumber" required name="PassportNumber" value="<?=$pa['PassportNumber'] ?? ''?>" placeholder="Enter NRIC/passport number">
							</div>
							<label for="Citizenship" class="col-sm-2 col-form-label">Citizenship:</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="Citizenship" required name="Citizenship" value="<?=$pa['Citizenship'] ?? ''?>" placeholder="Enter citizenship">
							</div>
							<label for="Gender" class="col-sm-1 col-form-label">Gender:</label>
							<div class="col-sm-3">
								<select class="form-control" id="Gender" name="Gender">
									<option value="male" <?=$pa['Gender'] ?? '' == 'male' ? 'selected' : '' ?>>Male</option>
									<option value="female" <?=$pa['Gender'] ?? '' == 'female' ? 'selected' : '' ?>>Female</option>
								</select>
							</div>
						</div>
						<div class="form-group row mb-2">
							<label for="SpouseNAddress" class="col-sm-5 col-form-label">Are You Serving Bond With Your Present Employer?</label>
							<div class="col-sm-5">
								<div class="form-check form-check-inline mt-2">
									<input class="form-check-input" type="radio" name="PresentEmployerBond" <?=$pa['PresentEmployerBond'] ?? '' == 'yes' ? "checked" : "" ?> id="PresentEmployerBondYes" value="yes">
									<label class="form-check-label" for="PresentEmployerBondYes">Yes</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="PresentEmployerBond" <?=$pa['PresentEmployerBond'] ?? '' == 'no' ? "checked" : "" ?> id="PresentEmployerBondNo" value="no">
									<label class="form-check-label" for="PresentEmployerBondNo">No</label>
								</div>
							</div>
						</div>

						<h6 class="mb-4">TAB Global is an equal opportunity employer for people of all background. The following social information is intended only to give us a sense of your origins and general background. </h6>

						<div class="form-group row mb-2">
							<label for="Birthday" class="col-sm-3 col-form-label">Date of Birth:</label>
							<div class="col-sm-3">
								<input type="date" class="form-control" id="Birthday" name="Birthday" value="<?=$pa['Birthday'] ?? ''?>" required>
							</div>
							<label for="BirthPlace" class="col-sm-2 col-form-label">Birth Place:</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="BirthPlace" name="BirthCity" value="<?=$pa['BirthCity'] ?? ''?>" required placeholder="City">
							</div>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="BirthPlace" name="BirthCountry" value="<?=$pa['BirthCountry'] ?? ''?>" required placeholder="Country">
							</div>
							</div>
							<div class="form-group row mb-2">
							<label for="Dialect" class="col-sm-3 col-form-label">Ethnicity/Dialect:</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="Dialect" name="Dialect" value="<?=$pa['Dialect'] ?? ''?>" required placeholder="Enter ethnicity/dialect">
							</div>
							<label for="Religion" class="col-sm-3 col-form-label">Religion:</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="Religion" name="Religion" value="<?=$pa['Religion'] ?? ''?>" required placeholder="Enter religion">
							</div>
						</div>

						<div class="form-group row mb-2">
							
							<label for="MaritalStatus" class="col-sm-3 col-form-label">Marital Status:</label>
							<div class="col-sm-9">
								<div class="form-check form-check-inline mt-2">
									<input class="form-check-input" type="radio" name="MaritalStatus" id="MaritalStatusSingle" <?=$pa['MaritalStatus'] ?? '' == 'Single' ? 'checked' : '' ?> value="Single" required checked>
									<label class="form-check-label" for="MaritalStatusSingle">Single</label>
								</div>
								<div class="form-check form-check-inline mt-2">
									<input class="form-check-input" type="radio" name="MaritalStatus" id="MaritalStatusMarried" <?=$pa['MaritalStatus'] ?? '' == 'Married' ? 'checked' : '' ?> value="Married" required>
									<label class="form-check-label" for="MaritalStatusMarried">Married</label>
								</div>
								<div class="form-check form-check-inline mt-2">
									<input class="form-check-input" type="radio" name="MaritalStatus" id="MaritalStatusSeparated" <?=$pa['MaritalStatus'] ?? '' == 'Separated' ? 'checked' : '' ?> value="Separated" required>
									<label class="form-check-label" for="MaritalStatusSeparated">Separated</label>
								</div>
								<div class="form-check form-check-inline mt-2">
									<input class="form-check-input" type="radio" name="MaritalStatus" id="MaritalStatusDivorced" <?=$pa['MaritalStatus'] ?? '' == 'Divorced' ? 'checked' : '' ?> value="Divorced" required>
									<label class="form-check-label" for="MaritalStatusDivorced">Divorced</label>
								</div>
								<div class="form-check form-check-inline mt-2">
									<input class="form-check-input" type="radio" name="MaritalStatus" id="MaritalStatusWidowed" <?=$pa['MaritalStatus'] ?? '' == 'Widowed' ? 'checked' : '' ?> value="Widowed" required>
									<label class="form-check-label" for="MaritalStatusWidowed">Widowed</label>
								</div>
							</div>
						</div>
						
						<div class="separator01"></div>

						<div class="form-group row mb-2">
							<label for="SpouseName" class="col-sm-2 col-form-label">If marries, State spouse's Name:</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="SpouseName" name="SpouseName" value="<?=$pa['SpouseName'] ?? ''?>" placeholder="Spouse's name">
							</div>
							<label for="Occupation" class="col-sm-2 col-form-label">Occupation:</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="Occupation" name="Occupation" value="<?=$pa['Occupation'] ?? ''?>" placeholder="Enter occupation">
							</div>
						</div>

						<div class="form-group row mb-2">
							<label for="NumberofChildren" class="col-sm-2 col-form-label">No. of Children:</label>
							<div class="col-sm-5">
								<input type="number" min="0" class="form-control" id="NumberofChildren" name="NumberofChildren" value="<?=$pa['NumberofChildren'] ?? ''?>">
							</div>
							<label for="AgeRange" class="col-sm-2 col-form-label">Age Range:</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="AgeRange" name="AgeRange" value="<?=$pa['AgeRange'] ?? ''?>" placeholder="Enter age range">
							</div>
						</div>

						<div class="form-group row mb-2">
							<label for="NextOfKinName" class="col-sm-2 col-form-label">Next-of-kin's:</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="NextOfKinName" name="NextOfKinName" value="<?=$pa['NextOfKinName'] ?? ''?>" placeholder="Enter name">
							</div>
							<label for="Relationship" class="col-sm-2 col-form-label">Relationship:</label>
							<div class="col-sm-3"><input type="text" class="form-control" id="Relationship" name="Relationship" value="<?=$pa['Relationship'] ?? ''?>" placeholder="Enter relationship">
							</div>
						</div>

						<div class="form-group row mb-2">
							<label for="SpouseNAddress" class="col-sm-2 col-form-label">Address:</label>
							<div class="col-sm-5">
								<input type="text" class="form-control" id="SpouseAddress" name="SpouseAddress" value="<?=$pa['SpouseAddress'] ?? ''?>" placeholder="Enter spouse's address">
							</div>
							<label for="SpouseTelephoneNumber" class="col-sm-2 col-form-label">Tel No:</label>
							<div class="col-sm-3"><input type="tel" class="form-control phone" id="SpouseTelephoneNumber" name="SpouseTelephoneNumber" value="<?=$pa['SpouseTelephoneNumber'] ?? '' != "" ? "+".$pa['SpouseTelephoneNumber'] ?? '' : '' ?>">
							</div>
						</div>

						<div class="form-group row mb-2">
							<label for="PositionDesired" class="col-sm-2 col-form-label">Position Desired:</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="PositionDesired" name="PositionDesired" value="<?=$pa['PositionDesired'] ?? ''?>">
							</div>
							<label for="DateAvailable" class="col-sm-2 col-form-label">Date Available:</label>
							<div class="col-sm-4">
								<input type="date" class="form-control" id="DateAvailable" name="DateAvailable" value="<?=$pa['DateAvailable'] ?? ''?>" required>
							</div>
						</div>

						<div class="form-group row mb-2">
							<label for="PositionQualified" class="col-sm-2 col-form-label PositionQualified">Other Positions Which Your Are Qualified:</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="PositionQualified" name="PositionQualified" value="<?=$pa['PositionQualified'] ?? ''?>" placeholder="Write positions">
							</div>
							<label for="PreviouslyEmployedToCompany" class="col-sm-2 col-form-label PreviouslyEmployedToCompany">Previously employed by/applied to join Company:</label>
							<div class="col-sm-4">
								<div class="form-check form-check-inline mt-2">
									<input class="form-check-input" type="radio" name="PreviouslyEmployedToCompany" id="PreviouslyEmployedToCompanyYes" <?=$pa['PreviouslyEmployedToCompany'] ?? '' == 'yes' ? 'checked' : '' ?> value="yes">
									<label class="form-check-label" for="PreviouslyEmployedToCompanyYes">Yes</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="PreviouslyEmployedToCompany" id="PreviouslyEmployedToCompanyNo" <?=$pa['PreviouslyEmployedToCompany'] ?? '' == 'no' ? 'checked' : '' ?> value="no">
									<label class="form-check-label" for="PreviouslyEmployedToCompanyNo">No</label>
								</div>
							</div>
						</div>
						<div class="form-group row mb-2">
							<label for="RelativesInCompany" class="col-sm-3 col-form-label">Do you have any friends/relatives in the company</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="RelativesInCompany" name="RelativesInCompany" value="<?=$pa['RelativesInCompany'] ?? ''?>" placeholder="Leave blank if none">
							</div>
						</div>
						<div class="d-flex justify-content-end">
							<button type="submit" class="btn btn-primary">Save</button>
						</div>
					</form>
				</section>

				<!--educational details section-->
				<hr>
				<section id="form-section-2" class="p-4 bg-light" data-position="2" data-table="ApplicationEducationalDetails">
					<h5 class="my-4">
						<h3>Educational Details</h3>
					</h5>
					<div><b class="text-warning">NOTE:</b> <small>Please list in chronological order, from primary to university where applicable</small></div>
					<form id="form-employee-2">
						<table width="100%" class="table" id="educational-details-table">
							<tr>
								<td align="center">Name of School</td>
								<td align="center">Address</td>
								<td align="center">Level</td>
								<td align="center">From</td>
								<td align="center">To</td>
								<td align="center">Did You Graduate</td>
								<td colspan="2" align="center">Details</td>
							</tr>
							<?php if(isset($educational_details) && count($educational_details) > 0) { foreach ($educational_details as $index => $educational_detail) { ?>
							<tr class="educational-details-row">
								<td>
									<input type="text" name="SchoolName[]" value="<?=$educational_detail['SchoolName']?>" class="form-control" placeholder="Enter School">
								</td>
								<td>
									<input type="text" name="SchoolCity[]" value="<?=$educational_detail['SchoolCity']?>" class="form-control mb-1" placeholder="City">
									<input type="text" name="SchoolCountry[]" value="<?=$educational_detail['SchoolCountry']?>" class="form-control" placeholder="Country">
								</td>
								<td>
									<select name="EducationalDetailsLevelID[]" class="form-control">
										<?php foreach($educational_levels as $row){ ?>
										<option value="<?=$row['EducationalDetailsLevelID']?>" <?=$educational_detail['EducationalDetailsLevelID'] == $row['EducationalDetailsLevelID'] ? 'selected' : '' ?>><?=$row['LevelName']?></option>
										<?php } ?>
									</select>
								</td>
								<td><input type="month" name="AttendedFrom_<?=$index?>" class="form-control AttendedFrom" value="<?=date_format(date_create($educational_detail['AttendedFrom']), "Y-m")?>"></td>
								<td><input type="month" name="AttendedTo_<?=$index?>" class="form-control AttendedTo" value="<?=date_format(date_create($educational_detail['AttendedTo']), "Y-m")?>"></td>
								<td>
									<div class="form-check form-check-inline mt-2">
										<input class="form-check-input" type="radio" name="IsGraduated_<?=$index?>" value="yes" <?=$educational_detail['IsGraduated'] == 'yes' ? 'checked' : '' ?>>
										<label class="form-check-label" for="IsGraduatedYes">Yes</label>
									</div>
									<div class="form-check form-check-inline">
										<input class="form-check-input" type="radio" name="IsGraduated_<?=$index?>" value="no" <?=$educational_detail['IsGraduated'] == 'no' ? 'checked' : '' ?>>
										<label class="form-check-label" for="IsGraduatedNo">No</label>
									</div>
								</td>
								<td>
									<input type="text" name="SchoolDetails[]" class="form-control" value="<?=$educational_detail['SchoolDetails']?>">
								</td>
								<td style="position: relative;"><button style="<?=count($educational_details) == 1 ? 'display: none;' : '' ?>margin-top: 1px;margin-right: 3px; position: absolute;top: 0;right: 0;padding: 1px 5px 1px 5px;" type="button" class="btn btn-rounded-sm btn-danger remove-row-btn"><i class="fa-solid fa-xmark"></i></button></td>
							</tr>
							<?php }} else { ?>
							<tr class="educational-details-row">
								<td>
									<input type="text" name="SchoolName[]" class="form-control" placeholder="Enter School">
								</td>
								<td>
									<input type="text" name="SchoolCity[]" class="form-control mb-1" placeholder="City">
									<input type="text" name="SchoolCountry[]" class="form-control" placeholder="Country">
								</td>
								<td>
									<select name="EducationalDetailsLevelID[]" class="form-control">
										<?php foreach($educational_levels as $row){ ?>
										<option value="<?=$row['EducationalDetailsLevelID']?>"><?=$row['LevelName']?></option>
										<?php } ?>
									</select>
								</td>
								<td><input type="month" name="AttendedFrom_0" class="form-control AttendedFrom"></td>
								<td><input type="month" name="AttendedTo_0" class="form-control AttendedTo"></td>
								<td>
									<div class="form-check form-check-inline mt-2">
										<input class="form-check-input" type="radio" name="IsGraduated_0" value="yes" checked>
										<label class="form-check-label" for="IsGraduatedYes">Yes</label>
									</div>
									<div class="form-check form-check-inline">
										<input class="form-check-input" type="radio" name="IsGraduated_0" value="no">
										<label class="form-check-label" for="IsGraduatedNo">No</label>
									</div>
								</td>
								<td>
									<input type="text" name="SchoolDetails[]" class="form-control">
								</td>
								<td style="position: relative;"><button style="display: none;margin-top: 1px;margin-right: 3px; position: absolute;top: 0;right: 0;padding: 1px 5px 1px 5px;" type="button" class="btn btn-rounded-sm btn-danger remove-row-btn"><i class="fa-solid fa-xmark"></i></button></td>
							</tr>
							<?php } ?>
						</table>
						<div class="d-flex justify-content-between">
							<button type="button" class="btn btn-primary" id="add-new-school">+ School</button>
							<button type="submit" class="btn btn-primary">Save</button>
						</div>
					</form>
					<hr>
					<h3>Further Education</h3>
					<form id="form-employee-2half">
						<div class="form-group my-2">
							<label for="FurtherEducation" class="col-form-label">If you Plan Further Education, Please Explain:</label>
							<textarea type="text" class="form-control" id="FurtherEducation" name="FurtherEducation"><?=$pa['FurtherEducation'] ?? ''?></textarea>
						</div>

						<div class="form-group mb-2">
							<label for="TrainingSkills" class="col-form-label">Other Training Or Skills (eg Technology, skills based, special courses etc):</label>
							<textarea type="text" class="form-control" id="TrainingSkills" name="TrainingSkills"><?=$pa['TrainingSkills'] ?? ''?></textarea>
						</div>

						<div class="form-group mb-2">
							<label for="FullName" class="col-form-label">Hobbies:</label>
							<textarea type="text" class="form-control" id="Hobbies" name="Hobbies"><?=$pa['Hobbies'] ?? ''?></textarea>
						</div>
						<div class="d-flex justify-content-end">
							<button type="submit" class="btn btn-primary">Save</button>
						</div>
					</form>
				</section>  

				<!--national services section-->
				<section id="form-section-3" class="p-4 bg-light" data-position="3" data-table="ApplicationNationalServices">
					<div>
						<h3>National Service</h3>
					</div>
					<div class="form-group mb-3">
						<div class="form-check form-check-inline mt-2">
							<input class="form-check-input" type="radio" name="NationalServiceApplicable" value="1" <?=$ens != NULL && $ens['Applicable'] == 1 ? 'checked' : '' ?>>
							<label class="form-check-label" for="NationalServiceApplicableYes">Applicable</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="NationalServiceApplicable" value="0" <?=$ens == NULL || $ens['Applicable'] == 0 ? 'checked' : '' ?>>
							<label class="form-check-label" for="NationalServiceApplicableNo">Not Applicable</label>
						</div>
					</div>
					<form id="form-employee-3" style="<?=$ens == NULL || $ens['Applicable'] == 0 ? 'display: none;' : '' ?>">
						<input type="hidden" name="Applicable" value="<?=$ens != NULL ? $ens['Applicable'] : '0' ?>">
						<table width="100%" class="table table-bordered">
							<tr>
								<td align="center">FULL TIME</td>
								<td align="center">From</td>
								<td align="center">To</td>
								<td align="center">Type of Discharge</td>
								<td align="center">Vocation</td>
								<td align="center">Next In-Camp Training</td>
								<td align="center">Last Rank</td>
							</tr>
							<tr>
								<td>
									<input type="text" id="FullTime" name="FullTime" value="<?=$ens != NULL ? $ens['FullTime'] : '' ?>" class="form-control">
								</td>
								<td>
									<input type="date" id="ServedFrom" name="ServedFrom" value="<?=$ens != NULL ? $ens['ServedFrom'] : '' ?>" class="form-control">
								</td>
								<td><input type="date" id="ServedTo" name="ServedTo" value="<?=$ens != NULL ? $ens['ServedTo'] : '' ?>" class="form-control">
								</td>
								<td>
									<input type="text" id="DischargeType" name="DischargeType" value="<?=$ens != NULL ? $ens['DischargeType'] : '' ?>" class="form-control">
								</td>
								<td>
									<input type="text" id="Vocation" name="Vocation" value="<?=$ens != NULL ? $ens['Vocation'] : '' ?>" class="form-control">
								</td>
								<td>
									<input type="text" id="NextInCampTraining" name="NextInCampTraining" value="<?=$ens != NULL ? $ens['NextInCampTraining'] : '' ?>" class="form-control">
								</td>
								<td>
									<input type="text" id="LastRank" name="LastRank" value="<?=$ens != NULL ? $ens['LastRank'] : '' ?>" class="form-control">
								</td>
							</tr>
						</table>
						<div class="form-group row my-2">
							<label for="ServiceSchools" class="col-sm-2 col-form-label">Service Schools Or Special Experience:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="ServiceSchools" name="ServiceSchools" value="<?=$ens != NULL ? $ens['ServiceSchools'] : '' ?>">
							</div>
						</div>
						<div class="form-group row mb-2">
							<label for="SEPartTime" class="col-sm-2 col-form-label">PART TIME:</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="SEPartTime" name="SEPartTime" value="<?=$ens != NULL ? $ens['SEPartTime'] : '' ?>">
							</div>
							<label for="SEUnitAttached" class="col-sm-2 col-form-label">Unit Attached to:</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="SEUnitAttached" name="SEUnitAttached" value="<?=$ens != NULL ? $ens['SEUnitAttached'] : '' ?>">
							</div>
							<label for="SEDurLiability" class="col-sm-2 col-form-label">Duration of Liability:</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="SEDurLiability" name="SEDurLiability" value="<?=$ens != NULL ? $ens['SEDurLiability'] : '' ?>">
							</div>
						</div>
						<div class="form-group row mb-2">
							<label for="SEFreqDuties" class="col-sm-2 col-form-label">Frequency of Duties:</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="SEFreqDuties" name="SEFreqDuties" value="<?=$ens != NULL ? $ens['SEFreqDuties'] : '' ?>">
							</div>
							<label for="SELastRank" class="col-sm-2 col-form-label">Last Rank:</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="SELastRank" name="SELastRank" value="<?=$ens != NULL ? $ens['SELastRank'] : '' ?>">
							</div>
							<label for="SEStatus" class="col-sm-2 col-form-label">Exempted/Defered/Awaiting:</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="SEStatus" name="SEStatus" value="<?=$ens != NULL ? $ens['SEStatus'] : '' ?>">
							</div>
						</div>
						<div class="form-group row mb-2">
							<label for="SEPeriod" class="col-sm-2 col-form-label">Period/Date of Registration:</label>
							<div class="col-sm-2">
								<input type="date" class="form-control" id="SEPeriod" name="SEPeriod" value="<?=$ens != NULL ? $ens['SEPeriod'] : '' ?>">
							</div>
							<label for="SEStatusReason" class="col-sm-2 col-form-label">Reason(s):</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="SEStatusReason" name="SEStatusReason" value="<?=$ens != NULL ? $ens['SEStatusReason'] : '' ?>">
							</div>
						</div>
						<div class="d-flex justify-content-end">
							<button type="submit" class="btn btn-primary">Save</button>
						</div>
					</form>
				</section> 

				<!--employment history section-->
				<section id="form-section-4" class="p-4 bg-light" data-position="4" data-table="ApplicationEmploymentHistory">
					<form id="form-employee-4">
						<h5 class="my-4">
							<h3>Employment History</h3>
						</h5>
						<table width="100%" class="table" id="tbl-emphistory">
							<tr>
								<td align="center">Name of Employer</td>
								<td align="center">Address of Employer</td>
								<td align="center">Position</td>
								<td align="center">From</td>
								<td align="center">To</td>
								<td align="center">Salary</td>
								<td align="center" colspan="2">Reasons for Leaving</td>
							</tr>
							<?php if(isset($employemt_histories) && count($employemt_histories) > 0){ foreach($employemt_histories as $index => $employemt_history) {?>
							<tr class="employee-history-row">
								<td>
									<input type="text" name="EmployerName[]" value="<?=$employemt_history['EmployerName']?>" class="form-control" placeholder="Employer name">
								</td>
								<td>
									<input type="text" name="EmployerCity[]" value="<?=$employemt_history['EmployerCity']?>" class="form-control" placeholder="City">
									<input type="text" name="EmployerCountry[]" value="<?=$employemt_history['EmployerCountry']?>" class="form-control" placeholder="Country">
								</td>
								<td><input type="text" name="Position[]" value="<?=$employemt_history['Position']?>" class="form-control" placeholder="Position"></td>
								<td><input type="month" name="EmploymentFrom_<?=$index?>" value="<?=date_format(date_create($employemt_history['EmploymentFrom']), "Y-m")?>" class="form-control EmploymentFrom"></td>
								<td><input type="month" name="EmploymentTo_<?=$index?>" value="<?=date_format(date_create($employemt_history['EmploymentTo']), "Y-m")?>" class="form-control EmploymentTo"></td>
								<td>
									<div class="input-group">
										<input type="text" name="Salary[]" value="<?=$employemt_history['Salary']?>" class="form-control" placeholder="Salary">
										<div class="input-group-append">
											<span class="input-group-text">$</span>
										</div>
									</div>
								</td>
								<td>
									<input type="text" name="ReasonForLeaving[]" value="<?=$employemt_history['ReasonForLeaving']?>" class="form-control" placeholder="Enter reasons">
								</td>
								<td style="position: relative;"><button style="<?=count($employemt_histories) == 1 ? 'display: none;' : ''?>margin-top: 1px;margin-right: 3px; position: absolute;top: 0;right: 0;padding: 1px 5px 1px 5px;" type="button" class="btn btn-rounded-sm btn-danger remove-row-btn"><i class="fa-solid fa-xmark"></i></button></td>
							</tr>
							<?php }} else {?>
							<tr class="employee-history-row">
								<td>
									<input type="text" name="EmployerName[]" class="form-control" placeholder="Employer name">
									<input type="hidden" name="ApplicationEmploymentHistoryID[]" class="form-control">
								</td>
								<td>
									<input type="text" name="EmployerCity[]" class="form-control" placeholder="City">
									<input type="text" name="EmployerCountry[]" class="form-control" placeholder="Country">
								</td>
								<td><input type="text" name="Position[]" class="form-control" placeholder="Position"></td>
								<td><input type="month" name="EmploymentFrom_0" class="form-control EmploymentFrom"></td>
								<td><input type="month" name="EmploymentTo_0" class="form-control EmploymentTo"></td>
								<td>
									<div class="input-group">
										<input type="text" name="Salary[]" class="form-control" placeholder="Salary">
										<div class="input-group-append">
											<span class="input-group-text">$</span>
										</div>
									</div>
								</td>
								<td>
									<input type="text" name="ReasonForLeaving[]" class="form-control" placeholder="Enter reasons">
								</td>
								<td style="position: relative;"><button style="display: none;margin-top: 1px;margin-right: 3px; position: absolute;top: 0;right: 0;padding: 1px 5px 1px 5px;" type="button" class="btn btn-rounded-sm btn-danger remove-row-btn"><i class="fa-solid fa-xmark"></i></button></td>
							</tr>
							<?php } ?>
						</table>
						<div class="d-flex justify-content-between">
							<a id="add-emphistory" class="btn btn-primary float-end">+ Employment History</a>
							<button type="submit" class="btn btn-primary">Save</button>
						</div>
					</form>
				</section> 

				<!--languages section-->
				<section id="form-section-5" class="p-4 bg-light" data-position="5" data-table="Applications">
					<form id="form-employee-5">
						<h5 class="my-4">
							<h3>Languages</h3>
						</h5>
						<div class="form-group my-2">
							<label for="LanguageSpoken">Language Spoken:</label>
							<input type="text" class="form-control" id="LanguageSpoken" name="LanguageSpoken" value="<?=$pa['LanguageSpoken'] ?? ''?>">
						</div>
						<div class="form-group my-2">
							<label for="LanguageWritten">Language Written:</label>
							<input type="text" class="form-control" id="LanguageWritten" name="LanguageWritten" value="<?=$pa['LanguageWritten'] ?? ''?>">
						</div>
						<div class="d-flex justify-content-end">
							<button type="submit" class="btn btn-primary">Save</button>
						</div>
					</form>
				</section> 

				<!--medical history section-->
				<section id="form-section-6" class="p-4 bg-light" data-position="6" data-table="Applications">
					<form id="form-employee-6">
						<h5 class="my-4">
							<h3>Medical History</h3>
						</h5>
						<div class="form-group my-2">
							<label for="PhysicalDisabledDetails">Any Physical Disability:   No / Yes, Please Specify:</label>
							<input type="text" class="form-control" id="PhysicalDisabledDetails" name="PhysicalDisabledDetails" value="<?=$pa['PhysicalDisabledDetails'] ?? ''?>">
						</div>
						<div class="form-group my-2">
							<label for="MajorIllnessDetails">Any Major Illiness / Accident in Last Six Months?   No / Yes, Please Specify:</label>
							<input type="text" class="form-control" id="MajorIllnessDetails" name="MajorIllnessDetails" value="<?=$pa['MajorIllnessDetails'] ?? ''?>">
						</div>
						<p class="text-warning"><b>NOTE: </b>This section is for information only, to help us work with you better.</p>
						<div class="d-flex justify-content-end">
							<button type="submit" class="btn btn-primary">Save</button>
						</div>
					</form>
				</section> 

				<!--references section-->
				<section id="form-section-7" class="p-4 bg-light" data-position="7" data-table="ApplicationReferences">
					<form id="form-employee-7">
						<div class="my-4">
							<h3>References</h3>
							<small>Please provide the contacts of at least two people who can provide us with insights into your suitability for your career with us.</small>
						</div>
						<table width="100%" class="table table-bordered" id="tbl-reference">
							<tr>
								<td align="center">Name</td>
								<td align="center">Association</td>
								<td align="center">Email</td>
								<td align="center">Mobile</td>
								<td align="center">Years Known</td>
							</tr>
							<?php if (isset($application_references) && count($application_references) > 0) { foreach ($application_references as $index => $application_reference) { ?>
							<tr class="reference-row">
								<td>
									<input type="text" name="ReferenceName[]" value="<?=$application_reference['ReferenceName']?>" class="form-control" placeholder="Enter name">
								</td>
								<td>
									<textarea type="text" name="Association[]" class="form-control" placeholder="Organization where you worked with this person"><?=$application_reference['Association']?></textarea>
								</td>
								<td><input type="email" name="ReferenceEmail[]" value="<?=$application_reference['ReferenceEmail']?>" class="form-control" placeholder="Email"></td>
								<td><input type="tel" name="ReferenceMobile[]" value="<?=$application_reference['ReferenceMobile']?>" class="form-control phone"></td>
								<td><input type="number" name="YearsKnown[]" value="<?=$application_reference['YearsKnown']?>" class="form-control" placeholder="Years known" min="0"></td>
							</tr>
							<?php }} else {?>
							<tr class="reference-row">
								<td>
									<input type="text" name="ReferenceName[]" class="form-control" placeholder="Enter name">
									<input type="hidden" name="ApplicationReferencesID[]" class="form-control">
								</td>
								<td>
									<textarea type="text" name="Association[]" class="form-control" placeholder="Organization where you worked with this person"></textarea>
								</td>
								<td><input type="email" name="ReferenceEmail[]" class="form-control" placeholder="Email"></td>
								<td><input type="tel" name="ReferenceMobile[]" class="form-control phone"></td>
								<td><input type="number" name="YearsKnown[]" class="form-control" placeholder="Years known" min="0"></td>
							</tr>
							<?php } ?>
						</table>
						<div class="d-flex justify-content-between">
							<a class="btn btn-primary float-end" id="add-reference">+ New Reference</a>
							<button type="submit" class="btn btn-primary">Save</button>
						</div>
					</form>
				</section>
			</div>
			<div class="tab-pane" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item" role="presentation">
						<button class="nav-link active" id="my-reviews-tab" data-bs-toggle="tab" data-bs-target="#my-reviews" type="button" role="tab" aria-controls="my-reviews" aria-selected="true">My Reviews</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" id="reviews-to-me-tab" data-bs-toggle="tab" data-bs-target="#reviews-to-me" type="button" role="tab" aria-controls="reviews-to-me" aria-selected="false">Reviews Submitted to me</button>
					</li>
				</ul>
				<div class="tab-content mt-4">
					<div class="tab-pane active" id="my-reviews" role="tabpanel" aria-labelledby="reviews-tab">
						<table class="table table-primary table-hover tr-link" id="table-my-reviews" data-id="<?=$e['EmployeeID']?>">
							<thead>
								<tr>
									<th class="sortable" data-order="DESC" data-column="EmployeeID"><div>Review ID<i class="arrow arrow-up active"></i></div></th>
									<th class="sortable" data-order="ASC" data-column="ReviewTypeName"><div>Review Type<i class="arrow arrow-up"></i></div></th>
									<th class="sortable" data-order="ASC" data-column="ReviewStatusName"><div>Review Status<i class="arrow arrow-up"></i></div></th>
									<th>Actions</th>
								</tr>
				   			</thead>
				   			<tbody></tbody>
						</table>
						<div class="text-center" id="employee-my-reviews-loader" style="display: none;">
							<div id="loading"></div>
						</div>
					</div>
					<div class="tab-pane" id="reviews-to-me" role="tabpanel" aria-labelledby="reviews-tab">
						<table class="table table-primary table-hover tr-link" id="table-reviews" data-id="<?=$e['EmployeeID']?>">
							<thead>
								<tr>
									<th class="sortable" data-order="DESC" data-column="EmployeeID"><div>Review ID<i class="arrow arrow-up active"></i></div></th>
									<th class="sortable" data-order="ASC" data-column="ReviewTypeName"><div>Review Type<i class="arrow arrow-up"></i></div></th>
									<th class="sortable" data-order="ASC" data-column="ReviewStatusName"><div>Review Status<i class="arrow arrow-up"></i></div></th>
									<th class="sortable" data-order="ASC" data-column="FirstName"><div>Submitted By<i class="arrow arrow-up"></i></div></th>
									<th>Actions</th>
								</tr>
				   			</thead>
				   			<tbody></tbody>
						</table>
						<div class="text-center" id="employee-reviews-loader" style="display: none;">
							<div id="loading"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="applications" role="tabpanel" aria-labelledby="applications-tab">
				<table class="table table-primary" id="table-applications" data-id="<?=$e['EmployeeID']?>">
					<thead>
						<tr>
							<th class="sortable" data-order="DESC" data-column="ApplicationID"><div>ID<i class="arrow arrow-up active"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="FirstName"><div>Full Name<i class="arrow arrow-up"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="Email"><div>Email<i class="arrow arrow-up"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="TelephoneNumber"><div>Telephone<i class="arrow arrow-up"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="JobTitleName"><div>Job Applied to<i class="arrow arrow-up"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="ApplicationStatus"><div>Status<i class="arrow arrow-up"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="InterviewCount"><div>Interviewed<i class="arrow arrow-up"></i></div></th>
							<th></th>
			   		</thead>
					<tbody></tbody>
				</table>
				<div class="text-center" id="employee-applications-loader" style="display: none;">
					<div id="loading"></div>
				</div>
			</div>
			<div class="tab-pane" id="interviews" role="tabpanel" aria-labelledby="interviews-tab">
				<table class="table table-primary table-hover tr-link" id="table-interviews" data-id="<?=$e['EmployeeID']?>">
					<thead>
						<tr>
							<th class="sortable" data-order="DESC" data-column="FirstName" width="200px"><div>Interviewer<i class="arrow arrow-up active"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="CreatedAt"><div>Interview Created<i class="arrow arrow-up"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="StartedAt"><div>Interview Started<i class="arrow arrow-up"></i></div></th>
							<th>Time Spent (hr:mins)</th>
							<th class="sortable" data-order="ASC" data-column="ImpressionToHire"><div>Overall impression to hire<i class="arrow arrow-up"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="InterviewStatusName"><div>Status<i class="arrow arrow-up"></i></div></th>
							<th>Actions</th>
			   		</thead>
					<tbody></tbody>
				</table>
				<div class="text-center" id="employee-interviews-loader" style="display: none;">
					<div id="loading"></div>
				</div>
			</div>
			<div class="tab-pane" id="docs" role="tabpanel" aria-labelledby="docs-tab"></div>
			<div class="tab-pane" id="other-docs" role="tabpanel" aria-labelledby="other-docs-tab"></div>
			<div class="tab-pane" id="notes" role="tabpanel" aria-labelledby="notes-tab">			
				<div class="row mb-4">
					<div class="col text-right">
						<a class="btn btn-md btn-primary btn-rounded float-end" data-bs-toggle="modal" data-bs-target="#NotesModal">
							<i class="fas fa-plus"></i> Add note
						</a>
					</div>
				</div>
				<table class="table table-primary table-hover tr-link" id="table-notes" data-id="<?=$e['EmployeeID']?>">
					<thead>
						<tr>
							<th class="sortable" data-order="DESC" data-column="EmployeeNoteID"><div>Note ID<i class="arrow arrow-up active"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="Note"><div>Note<i class="arrow arrow-up"></i></div></th>
							<th class="sortable" data-order="ASC" data-column="CreatedDate"><div>Created Date<i class="arrow arrow-up"></i></div></th>
			   		</thead>
					<tbody>
					</tbody>
				</table>
				<div class="text-center" id="employee-notes-loader" style="display: none;">
					<div id="loading"></div>
				</div>
			</div>
			<div class="tab-pane" id="salary" role="tabpanel" aria-labelledby="salary-tab">
				<form id="salary-form" action="/core/save-salary.php" method="POST">
					<section class="p-4 bg-light" data-position="1" data-table="Employees">
						<div class="form-group row mb-2">
							<label for="BasicMonthlySalary" class="col-sm-2 col-form-label">Basic Monthly Salary:</label>
							<div class="col-sm-4">
								<input type="text" data-type="currency" class="form-control" id="BasicMonthlySalary" name="BasicMonthlySalary" value="<?=$salary['BasicMonthlySalary']??0?>">
							</div>
							<label for="GuaranteedAdditionalWage" class="col-sm-2 col-form-label">Guaranteed Additional Wage:</label>
							<div class="col-sm-4">
								<input type="text" data-type="currency" class="form-control" id="GuaranteedAdditionalWage" name="GuaranteedAdditionalWage" value="<?=$salary['GuaranteedAdditionalWage']??0 ?>">
							</div>
						</div>
						<div class="form-group row mb-2 d-flex align-items-center">
							<label for="SalesCommission" class="col-sm-2 col-form-label">Sales Commision:</label>
							<div class="col-sm-4">
								<input type="radio" id="SalesCommissionYes" name="SalesCommission" value="1" <?=(isset($salary['SalesCommission']) && $salary['SalesCommission'] == 1) ? 'checked' : '' ?> >Yes
								<input type="radio" id="SalesCommissionNo" name="SalesCommission" value="0" <?=(isset($salary['SalesCommission']) && $salary['SalesCommission'] == 0) ? 'checked' : '' ?>>No
							</div>
							<label for="DiscretionallyBonus" class="col-sm-2 col-form-label">Discretionally Bonus:</label>
							<div class="col-sm-4">
								<input type="radio" id="DiscretionallyBonusYes" name="DiscretionallyBonus" value="1" <?=(isset($salary['DiscretionallyBonus']) && $salary['DiscretionallyBonus'] == 1) ? 'checked' : '' ?>>Yes
								<input type="radio" id="DiscretionallyBonusNo" name="DiscretionallyBonus" value="0" <?=(isset($salary['DiscretionallyBonus']) && $salary['DiscretionallyBonus'] == 0) ? 'checked' : '' ?>>No
							</div>
							<label for="ProfitShare" class="col-sm-2 col-form-label">Profit Share:</label>
							<div class="col-sm-4">
								<input type="radio" id="ProfitShareYes" name="ProfitShare" value="1" <?=(isset($salary['ProfitShare']) && $salary['ProfitShare'] == 1) ? 'checked' : '' ?>>Yes
								<input type="radio" id="ProfitShareNo" name="ProfitShare" value="0" <?=(isset($salary['ProfitShare']) && $salary['ProfitShare'] == 0) ? 'checked' : '' ?>>No
							</div>
							<label for="Equity" class="col-sm-2 col-form-label">Equity:</label>
							<div class="col-sm-4">
								<input type="radio" id="EquityYes" name="Equity" value="1" <?=(isset($salary['Equity']) && $salary['Equity'] == 1) ? 'checked' : '' ?>>Yes
								<input type="radio" id="EquityNo" name="Equity" value="0" <?=(isset($salary['Equity']) && $salary['Equity'] == 0) ? 'checked' : '' ?>>No
							</div>
						</div>
						<div class="form-group row mb-2 d-flex align-items-center">
							<label for="ReviewDate" class="col-sm-2 col-form-label">Review Date:</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="ReviewDate" name="ReviewDate" value="<?=$salary['ReviewDate'] ?? ''?>" readonly>
							</div>

							<label for="NextReviewDate" class="col-sm-2 col-form-label">Next Review Date:</label>
							<div class="col-sm-4">
								<input type="date" class="form-control" name="NextReviewDate" readonly value="<?=$e['NextReviewDate'] ?? ''?>">
							</div>
						</div>
						<div class="form-group row mb-2 d-flex align-items-center">
							<label for="UpdatedDate" class="col-sm-2 col-form-label">Updated:</label>
							<div class="col-sm-4">
								<input type="text" readonly class="form-control" name="UpdatedDate" value="<?=$salary['UpdatedDate'] ?? ''?>">
							</div>
						</div>
						<div class="form-group row mb-2 d-flex align-items-center">
							<label for="Notes" class="col-12 col-form-label">Notes:</label>
							<div class="col-12">
								<textarea name="Notes" id="Notes" class="form-control" placeholder="Write salary notes"><?=$salary['Notes'] ?? ''?></textarea>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-12 text-end">
								<input type="hidden" name="EmployeeID" value="<?=$e['EmployeeID']?>">
								<button class="btn btn-success" type="submit">Save</button>
							</div>
						</div>
					</section>
				</form>
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
						<input type="hidden" class="form-control" id="EmployeeID" name="EmployeeID" value="<?=$_GET['id']?>">
						<input type="hidden" class="form-control" id="CreatedDate" name="CreatedDate" value="<?=date('Y-m-d h:i:s');?>">
						<input type="hidden" class="form-control" id="table" name="table" value="EmployeeNotes">
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
<!-- Nav tabs -->

<!-- Tab panes -->
          
<?php include("_footer.php"); ?>
